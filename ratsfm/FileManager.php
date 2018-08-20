<?php
/**
 * FileManager by Charmeryl
 * Author: jqjiang
 * Time:   2018/8/2 18:22
 */

namespace RatsFM;

class FileManager {
    protected $configs = [];

    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    public function run() {
        spl_autoload_register(array($this, 'load_class'));
        $this->set_timezone();
        $this->set_macros();
        $this->route();
    }

    public function set_timezone() {
        date_default_timezone_set($this->configs['system']['timezone']);
    }

    public function set_macros() {
        define("ENTER_URI", $this->configs['system']['rewrite']?BASE_URI:$_SERVER['SCRIPT_NAME']);
    }

    public function route() {
        $default['action'] = "index";
        $default['controller'] = "FileDirMgmtController";

        $action = $default['action'];
        $controller = 'RatsFM\\Controllers\\' . $default['controller'];
        $params = array_merge($_GET, $_POST);

        $uri = $_SERVER['REQUEST_URI'];
        $script = $_SERVER['SCRIPT_NAME'];

        $pos = strpos($uri, '?');
        $uri = $pos === false? $uri: substr($uri, 0, $pos);
        $uri = str_replace($script, '', $uri);
        $uri = rtrim($uri, '/');
        $params['uri'] = urldecode($uri);

        if(isset($params['action'])) {
            $action = $params['action'];
        }
        if (!class_exists($controller)) {
            exit($controller . '控制器不存在');
        }
        if (!method_exists($controller, $action)) {
            exit($action . '方法不存在');
        }

        $dispatch = new $controller($params, $this->configs);
        $dispatch->$action();

    }

    public function load_class($class_name) {
        $class_map = $this->class_map();
        if(isset($class_map[$class_name])) {
            $class_file = $class_map[$class_name];
        } else {
            return;
        }
        if(is_file($class_file)) {
            include($class_file);
        }
    }

    protected function class_map()
    {
        return [
            'RatsFM\Interfaces\FileInterface' => CORE_PATH . '/interfaces/'. $this->configs['storage']['backend'] .'/FileInterface.php',
            'RatsFM\Controllers\FileDirMgmtController' => CORE_PATH . '/FileDirMgmtController.php',
            'RatsFM\Frameworks\View' => CORE_PATH . '/frameworks/View.php',
        ];
    }
}
