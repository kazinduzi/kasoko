<?php

if (!empty($_GET['rt']) && $_GET['rt'] == 'favicon.ico') {
    exit(1);
}

/**
 * This file allows your kazinduzi application to work without .htaccess
 * by using the following url: http://www.yourhost.com/index.php/path/to/your/app
 *
 * This is discouraged over using a proper .htaccess rewrite.
 *
 * ONCE .htaccess IS NOT WORKING, DO UN-COMMENT THESE LINES OF CODE BELOW.
 */
/**
 * APPLICATION ENVIRONMENT
 *
 * Setting the environment for logging and error reporting.
 * This can be set to anything, but default usage is: {development | testing | production}
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
define('ENVIRONMENT', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development');
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);
defined('EXT') || define('EXT', '.php');
defined('CLASS_EXT') || define('CLASS_EXT', '.class.php');
defined('MODEL_EXT') || define('MODEL_EXT', '.model.php');
/**
 *  define the site path
 */
defined('KAZINDUZI_PATH') || define('KAZINDUZI_PATH', __DIR__);
defined('CORE_PATH') || define('CORE_PATH', (KAZINDUZI_PATH . '/core'));
defined('LIB_PATH') || define('LIB_PATH', (KAZINDUZI_PATH . '/library'));
defined('DB_PATH') || define('DB_PATH', (KAZINDUZI_PATH . '/database'));
defined('WIDGETS_PATH') || define('WIDGETS_PATH', (KAZINDUZI_PATH . '/widgets'));
defined('LAYOUT_PATH') || define('LAYOUT_PATH', (KAZINDUZI_PATH . '/elements' . '/layouts'));
defined('APP_PATH') || define('APP_PATH', (KAZINDUZI_PATH . '/application/backend'));
defined('MODULES_PATH') || define('MODULES_PATH', (KAZINDUZI_PATH . '/modules'));
defined('PLUGINS_PATH') || define('PLUGINS_PATH', (KAZINDUZI_PATH . '/plugins'));

defined('CONTROLLERS_PATH') || define('CONTROLLERS_PATH', (APP_PATH . '/controllers'));
defined('VIEWS_PATH') || define('VIEWS_PATH', (APP_PATH . '/views'));
defined('MODELS_PATH') || define('MODELS_PATH', (APP_PATH . '/models'));
defined('THEME_PATH') || define('THEME_PATH', (APP_PATH . '/theme'));

defined('CURRENT_URL') || define('CURRENT_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
defined('HOME_URL') || define('HOME_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
defined('SITE_URL') || define('SITE_URL', '/');
defined('KAZINDUZI_DEBUG') || define('KAZINDUZI_DEBUG', true);
defined('KAZINDUZI_START_TIME') || define('KAZINDUZI_START_TIME', microtime(true));
defined('KAZINDUZI_START_MEMORY') || define('KAZINDUZI_START_MEMORY', memory_get_usage());

require_once 'loader.php';

/**
 * Set the include path for the whole kazinduzi
 */
set_include_path(
        get_include_path()
        . PS . KAZINDUZI_PATH
        . PS . APP_PATH . '/configs'
        . PS . KAZINDUZI_PATH . '/includes'
        . PS . CONTROLLERS_PATH
        . PS . VIEWS_PATH
        . PS . LIB_PATH
        . PS . CORE_PATH
        . PS . APP_PATH
        . PS . DB_PATH
        . PS . WIDGETS_PATH
        . PS . KAZINDUZI_PATH . '/helpers'
        . PS . KAZINDUZI_PATH . '/elements'
        . PS . KAZINDUZI_PATH . '/html'
);

require 'Kazinduzi.php';
require 'init.php';
require 'common_functions.php';

$session = Kazinduzi::session();
$session->start();
require_once APP_PATH . '/bootstrap.php';
