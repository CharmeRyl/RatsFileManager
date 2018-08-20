<?php
/**
 * FileManager by Charmeryl
 * Author: jqjiang
 * Time:   2018/8/2 18:21
 */

// Set base directory
define('BASE_PATH', __DIR__);

// Set application directory
define('CORE_PATH', BASE_PATH . '/ratsfm');

// Set base uri
define('BASE_URI', substr($_SERVER['SCRIPT_NAME'],0,-10));

// Enable debug mode
define('APP_DEBUG', true);

// Load framework
require(CORE_PATH . '/FileManager.php');

// Load configurations
$config = require(BASE_PATH . '/config.php');

// Initialize
(new RatsFM\FileManager($config))->run();