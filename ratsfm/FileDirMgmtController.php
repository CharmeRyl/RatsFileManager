<?php
/**
 * FileManager by Charmeryl
 * Author: jqjiang
 * Time:   2018/8/3 9:39
 */

namespace RatsFM\Controllers;

use RatsFM\Interfaces\FileInterface;
use RatsFM\Frameworks\View;

class FileDirMgmtController {
    protected $params, $configs;
    protected $file_interface;

    function __construct($params, $configs) {
        $this->params = $params;
        $this->configs = $configs;
        $this->file_interface = new FileInterface($this->configs['storage']['path'] . $this->params['uri'],
            $this->configs['storage'][$this->configs['storage']['backend']]);
    }

    public function auth() {
        if($this->validate_login()) {
            header('location:' . ENTER_URI . $this->params['uri'] . '?action=index');
            exit('Authenticated');
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(!(isset($_POST['username']) && isset($_POST['password']))) {
                echo json_encode(array("status" => "failed"));
                return;
            }
            $auth = $this->configs['auth'];
            $user = $_POST['username'];
            $pass = $_POST['password'];
            if(!in_array($user, array_keys($auth['users'])) || $auth['users'][$user] != $pass) {
                echo json_encode(array("status" => "failed"));
                return;
            }
            $token = $this->generate_token($user, $pass);
            echo json_encode(array(
                'status' => 'success',
                'user' => base64_encode($user),
                'token' => base64_encode($token)
            ));
        } else {
            $auth_view = new View('auth', $this->configs);
            $auth_view->set_title($this->configs['system']['site_name']);
            $auth_view->set_css(array('floating-labels'));
            $auth_view->render();
        }
    }

    public function index() {
        if(!$this->validate_login()) {
            header('location:' . ENTER_URI . $this->params['uri'] . '?action=auth');
            exit('Authentication Error');
        }

        // generate breadcrumb
        $breadcrumb_keys = array_filter(explode('/', $this->params['uri']));
        $breadcrumb_urls = array();
        $url_prefix = ENTER_URI;
        foreach($breadcrumb_keys as $key) {
            array_push($breadcrumb_urls, $url_prefix . '/' . $key);
            $url_prefix = $url_prefix . '/' . $key;
        }
        array_unshift($breadcrumb_keys, 'Root');
        array_unshift($breadcrumb_urls, ENTER_URI . '/');

        // generate view
        $list = $this->construct_items();
        if($list === false) {
            http_response_code(404);
            exit("Directory not exist");
        }
        $index_view = new View('index', $this->configs);
        $index_view->set_title($this->configs['system']['site_name']);
        $index_view->set_css(array('base'));
        $index_view->assign('breadcrumb', array('keys' => $breadcrumb_keys, 'urls' => $breadcrumb_urls));
        $index_view->assign('items', $list);
        if($this->configs['auth']['enabled']) {
            $index_view->assign('username', base64_decode($_COOKIE['user']));
        }
        $index_view->render();
    }

    public function delete() {
        if(!$this->validate_login() || $this->params['name'] === '') {
            echo json_encode(array("status" => "failed", "info" => "prohibited"));
            return;
        }
        if($this->file_interface->remove_item(base64_decode(str_replace(' ', '+', $this->params['name'])))) {
            echo json_encode(array("status" => "success", "info" => ""));
        } else {
            echo json_encode(array("status" => "failed", "info" => "remove file error"));
        }
        return;
    }

    public function create() {
        if(!$this->validate_login()) {
            echo json_encode(array("status" => "failed", "info" => "prohibited"));
            return;
        }
        if($this->file_interface->make_directory(base64_decode(str_replace(' ', '+', $this->params['name'])))){
            echo json_encode(array("status" => "success", "info" => ""));
        } else {
            echo json_encode(array("status" => "failed", "info" => "create directory error"));
        }
        return;
    }

    public function upload() {
        if(!$this->validate_login()) {
            echo json_encode(array("status" => "failed", "info" => "prohibited"));
            return;
        }
        if($this->file_interface->upload_file($_FILES['upload'])) {
            echo json_encode(array("status" => "success", "info" => ""));
        } else {
            echo json_encode(array("status" => "failed", "info" => "upload file error"));
        }
        return;
    }

    public function rename() {
        if(!$this->validate_login() || $this->params['name'] === '') {
            echo json_encode(array("status" => "failed", "info" => "prohibited"));
            return;
        }
        $name = base64_decode(str_replace(' ', '+', $this->params['name']));
        $name_new = base64_decode(str_replace(' ', '+', $this->params['name_new']));
        if($this->file_interface->rename_item($name, $name_new)) {
            echo json_encode(array("status" => "success", "info" => ""));
        } else {
            echo json_encode(array("status" => "failed", "info" => "rename file error"));
        }
        return;
    }

    private function validate_login() {
        $user = isset($_COOKIE['user'])? base64_decode($_COOKIE['user']) : 'anonymous';
        $token = isset($_COOKIE['token'])? base64_decode($_COOKIE['token']) : '';
        $auth = $this->configs['auth'];
        if($auth['enabled'] == false) {
            return true;
        }
        if(in_array($user, array_keys($auth['users'])) && $token == $this->generate_token($user, $auth['users'][$user])) {
            return true;
        }
        return false;
    }

    private function generate_token($user, $pass) {
        return $this->encrypt($user . $pass, $this->configs['auth']['salt']);
    }

    private function encrypt($word, $salt) {
        $word = md5($word . $salt);
        $salt = substr($word,0,3);
        $encrypted = crypt($word, $salt);
        return $encrypted;
    }

    private function construct_items() {
        $list_old = $this->file_interface->get_list();
        if($list_old === false) {
            return false;
        }
        $list_new = array();
        foreach($list_old as $item) {
            if($this->is_hidden_file($item['name'])) {
                continue;
            }
            if(is_int($item['size'])) {
                $item['size'] = $this->format_size($item['size']);
            }
            $item['mtime'] = $this->format_time($item['mtime']);
            $item['url'] = $this->format_url($item['url'], $item['name']);
            array_push($list_new, $item);
        }
        $this->sort_items($list_new);
        return $list_new;
    }

    private function sort_items(&$items) {
        // sort the array
        array_multisort(array_column($items,'type'), SORT_DESC,
            array_column($items,'name'), $items);
    }

    private function is_hidden_file($name) {
        if(in_array($name, array('ratsfm',))) {
            return true;
        }
        if(substr($name, -4) == '.php') {
            return true;
        }
        if($name[0] == '.') {
            return true;
        }
        return false;
    }

    private function format_size($size) {
        $i = 0;
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        while(abs($size) >= 1024) {
            $size /= 1024;
            $i++;
            if($i == 5)  break;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    private function format_time($time) {
        return date("Y-m-d H:i:s", $time);
    }

    private function format_url($url, $name) {
        return array(
            'link' => ENTER_URI . $this->params['uri'] . '/' . $name,
            'download' => $url
        );
    }
}