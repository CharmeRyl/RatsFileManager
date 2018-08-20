<?php
/**
 * FileManager by Charmeryl
 * Author: jqjiang
 * Time:   2018/8/2 18:24
 */

namespace RatsFM\Interfaces;

class FileInterface {

    protected $path;

    public function __construct($rel_path, $configs) {
        $this->path = rtrim($rel_path, '/');
    }

    public function get_list() {
        if(!is_dir(BASE_PATH . $this->path)) {
            return false;
        }
        $list_name = scandir(BASE_PATH . $this->path);
        $list = array();

        // construct list array
        foreach($list_name as $name) {
            $item = array();
            $path = $this->construct_path($name);
            if(is_dir($path)) {
                $item['size'] = "N/A";
                $item['type'] = "folder";
                $item['url'] = '#';
            } else {
                $item['size'] = filesize($path);
                $item['type'] = "file";
                $item['url'] = $this->construct_url($name);
            }
            $item['name'] = $name;
            $item['mtime'] = filemtime($path);
            array_push($list, $item);
        }
        return $list;
    }

    public function upload_file($file) {
        if(!is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        return move_uploaded_file($file['tmp_name'], $this->construct_path($file['name']));
    }

    public function rename_item($name_old, $name_new) {
        if(!$this->validate_name($name_new)) {
            return false;
        }
        $path_src = $this->construct_path($name_old);
        $path_dst = $this->construct_path($name_new);
        return rename($path_src, $path_dst);
    }

    public function make_directory($name) {
        if(!$this->validate_name($name)) {
            return false;
        }
        if(empty($name)) return false;
        $path = $this->construct_path_unique($name);
        return mkdir($path, 0755, false);
    }

    public function remove_item($name) {
        if(!$this->validate_name($name)) {
            return false;
        }
        $path = $this->construct_path($name);
        if(is_dir($path)) {
            return $this->remove_directory($path);
        } else {
            return $this->remove_file($path);
        }
    }

    private function remove_file($path) {
        // delete from filesystem
        return unlink($path);
    }

    private function remove_directory($path) {
        if($handle=opendir($path)) {
            while(false !== ($item = readdir($handle))) {
                if($item != "." && $item != "..") {
                    if(is_dir($path . '/' . $item)) {
                        $this->remove_directory($path . '/' . $item);
                    } else {
                        unlink($path . '/' . $item);
                    }
                }
            }
        }
        closedir($handle);
        return rmdir($path);
    }

    private function construct_path_unique($name) {
        $path = $this->construct_path($name);
        $cnt = 1;
        while(is_dir($path)) {
            $path = $this->construct_path($name) . "($cnt)";
            $cnt++;
        }
        return $path;
    }

    private function construct_path($name) {
        return BASE_PATH . $this->path . '/' .$name;
    }

    private function construct_url($name) {
        return $this->path . '/' .$name;
    }

    private function validate_name($name) {
        if(strpos($name, '/') === false) {
            return true;
        }
        return false;
    }
}