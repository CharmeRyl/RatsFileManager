<?php
/**
 * FileManager by Charmeryl
 * Author: jqjiang
 * Time:   2018/8/6 12:26
 */

namespace RatsFM\Interfaces;

require 'phar://'. __DIR__ .'/cos-sdk-v5.phar/vendor/autoload.php';

use Qcloud;

class FileInterface {
    protected $path;
    protected $cos_client;
    protected $cos_configs;

    public function __construct($rel_path, $configs) {
        $this->path = rtrim($rel_path, '/');
        $this->cos_configs = $configs;
        $this->cos_client = new Qcloud\Cos\Client(array('region' => $this->cos_configs['region'],
            'credentials'=> $this->cos_configs['credentials']));
    }

    public function get_list() {
        try {
            $list = ($this->cos_client->listObjects(['Bucket' => $this->cos_configs['bucket']])->toArray())['Contents'];
        } catch (\Exception $e) {
            return false;
        }
        $list_new = array();
        foreach($list as $item) {
            $key = $item['Key'];
            if($this->path !== '') {
                $needle = ltrim($this->path, '/') . '/';
                if(strpos($item['Key'], $needle) === 0 && $item['Key'] !== $needle) {
                    $key = substr($item['Key'], strlen($needle));
                } else {
                    continue;
                }
            }
            $item_new = array();
            if(($pos = strpos($key, '/')) !== false) {
                $item_new['name'] = substr($key, 0, $pos);
                $item_new['type'] = 'folder';
                $item_new['size'] = 'N/A';
                $item_new['url'] = '#';
            } else {
                $item_new['name'] = $key;
                $item_new['type'] = 'file';
                $item_new['size'] = intval($item['Size']);
                $item_new['url'] = $this->get_object_url($item['Key']);
            }
            $item_new['mtime'] = date_timestamp_get(date_create($item['LastModified']));
            if(!in_array($item_new['name'], array_column($list_new, 'name'))) {
                array_push($list_new, $item_new);
            }
        }
        return $list_new;
    }

    public function upload_file($file) {
        try {
            $this->cos_client->putObject(array(
                'Bucket' => $this->cos_configs['bucket'],
                'Key' => $this->construct_key($file['name']),
                'Body'=> fopen($file['tmp_name'], 'rb')
            ));
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function rename_item($name_old, $name_new) {
        if(!$this->validate_name($name_new)) {
            return false;
        }
        $key_prefix_old = $this->construct_key($name_old);
        $key_prefix_new = $this->construct_key($name_new);
        try {
            $list = ($this->cos_client->listObjects(['Bucket' => $this->cos_configs['bucket']])->toArray())['Contents'];
        } catch (\Exception $e) {
            return false;
        }
        foreach (array_column($list, 'Key') as $key) {
            if($this->has_prefix($key_prefix_old, $key)) {
                $key_new = $key_prefix_new . substr($key, strlen($key_prefix_old));
                try {
                    if(substr($key, -1) === '/') {
                        $this->cos_client->putObject(array(
                            'Bucket' => $this->cos_configs['bucket'],
                            'Key' => $key_new,
                            'Body' => ''));
                        $this->cos_client->deleteObject(array(
                            'Bucket' => $this->cos_configs['bucket'],
                            'Key' => $key
                        ));
                    } else {
                        $this->cos_client->copyObject(array(
                            'Bucket' => $this->cos_configs['bucket'],
                            'CopySource' => $this->cos_configs['bucket']. '.cos.' . $this->cos_configs['region'] . '.myqcloud.com/' . $key,
                            'Key' => $key_new
                        ));
                        $this->cos_client->deleteObject(array(
                            'Bucket' => $this->cos_configs['bucket'],
                            'Key' => $key
                        ));
                    }
                } catch (\Exception $e) {
                    return false;
                }
            }
        }
        return true;
    }

    public function make_directory($name) {
        if(!$this->validate_name($name)) {
            return false;
        }
        $key = $this->construct_key($name);
        try {
            $this->cos_client->putObject(array(
                'Bucket' => $this->cos_configs['bucket'],
                'Key' => $key . '/',
                'Body' => ''));
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function remove_item($name) {
        if(!$this->validate_name($name)) {
            return false;
        }
        $key_prefix = $this->construct_key($name);
        try {
            $list = ($this->cos_client->listObjects(['Bucket' => $this->cos_configs['bucket']])->toArray())['Contents'];
        } catch (\Exception $e) {
            return false;
        }
        foreach (array_column($list, 'Key') as $key) {
            if($this->has_prefix($key_prefix, $key)) {
                try {
                    $this->cos_client->deleteObject(array(
                        'Bucket' => $this->cos_configs['bucket'],
                        'Key' => $key
                    ));
                } catch (\Exception $e) {
                    return false;
                }
            }
        }
        return true;
    }

    private function get_object_url($key) {
        if(isset($this->cos_configs['custom_url']) && !empty($this->cos_configs['custom_url'])) {
            $url = rtrim($this->cos_configs['custom_url'], '/') . "/$key";
        } else {
            $bucket = $this->cos_configs['bucket'];
            $region = $this->cos_configs['region'];
            $http_type = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
            $url = $http_type . "$bucket.cos.$region.myqcloud.com/$key";
        }
        return $url;
    }

    private function construct_key($name) {
        return ltrim($this->path . "/$name", '/');
    }

    private function has_prefix($prefix, $key) {
        if($key === $prefix) {
            return true;
        }
        if(strpos($key, $prefix) === 0 && substr($key, strlen($prefix), 1) === '/') {
            return true;
        }
        return false;
    }

    private function validate_name($name) {
        if(strpos($name, '/') === false) {
            return true;
        }
        return false;
    }
}