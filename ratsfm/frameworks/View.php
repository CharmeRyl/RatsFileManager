<?php
/**
 * FileManager by Charmeryl
 * Author: jqjiang
 * Time:   2018/8/4 17:37
 */

namespace RatsFM\Frameworks;


class View {
    protected $usr_vars = array();
    protected $sys_vars = array();
    protected $layout, $configs;

    function __construct($layout, $configs) {
        $this->layout = $layout;
        $this->configs = $configs;
        $this->load_sys_vars();
    }

    public function assign($name, $value) {
        $this->usr_vars[$name] = $value;
    }

    public function render() {
        extract($this->sys_vars);
        extract($this->usr_vars);

        $header = CORE_PATH . "/public/include/header.php";
        $footer = CORE_PATH . "/public/include/footer.php";

        $layout = CORE_PATH . "/public/$this->layout.php";

        include($header);
        include($layout);
        include($footer);
    }

    public function set_title($title) {
        $this->sys_vars['_TITLE'] = $title;
    }

    public function set_css($css) {
        if(!is_array($css)) {
            $css = array($css);
        }
        $this->sys_vars['_CSS'] = array_merge($this->sys_vars['_CSS'], $css);
    }

    private function load_sys_vars() {
        $this->sys_vars['_STATIC_URI'] = BASE_URI . '/ratsfm/public/static';
        $this->sys_vars['_SITE_NAME'] = $this->configs['system']['site_name'];
        $this->sys_vars['_CSS'] = array();
    }
}