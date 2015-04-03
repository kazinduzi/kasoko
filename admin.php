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
define('ENVIRONMENT', getenv('APPLICATION_ENV')?getenv('APPLICATION_ENV'):'development');

defined('DS') OR define('DS', DIRECTORY_SEPARATOR);
defined('PS') OR define('PS', PATH_SEPARATOR);
defined('EXT') OR define('EXT', '.php');
defined('CLASS_EXT') OR define('CLASS_EXT', '.class.php');
defined('MODEL_EXT') OR define('MODEL_EXT', '.model.php');
/**
 *  define the site path
 */
defined('KAZINDUZI_PATH') OR define('KAZINDUZI_PATH', realpath(dirname(__FILE__)));
defined('CORE_PATH') OR define('CORE_PATH', realpath(KAZINDUZI_PATH . DS . 'core'));
defined('LIB_PATH') OR define('LIB_PATH', realpath(KAZINDUZI_PATH . DS . 'library'));
defined('DB_PATH') OR define('DB_PATH', realpath(KAZINDUZI_PATH . DS . 'database'));
defined('WIDGETS_PATH') OR define('WIDGETS_PATH', realpath(KAZINDUZI_PATH . DS . 'widgets'));
defined('LAYOUT_PATH') OR define('LAYOUT_PATH', realpath(KAZINDUZI_PATH . DS . 'elements' . DS . 'layouts'));
defined('APP_PATH') OR define('APP_PATH', realpath(KAZINDUZI_PATH . DS . 'application/backend'));
defined('CONTROLLERS_PATH') OR define('CONTROLLERS_PATH', realpath(APP_PATH . DS . 'controllers'));
defined('VIEWS_PATH') OR define('VIEWS_PATH', realpath(APP_PATH . DS . 'views'));
defined('MODELS_PATH') OR define('MODELS_PATH', realpath(APP_PATH . DS . 'models'));
defined('THEME_PATH') || define('THEME_PATH', realpath(APP_PATH . DIRECTORY_SEPARATOR . 'theme'));

defined('CURRENT_URL') OR define('CURRENT_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
defined('HOME_URL') OR define('HOME_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
defined('SITE_URL') OR define('SITE_URL', '/');
defined('KAZINDUZI_DEBUG') OR define('KAZINDUZI_DEBUG', true);
defined('KAZINDUZI_START_TIME') OR define('KAZINDUZI_START_TIME', microtime(true));
defined('KAZINDUZI_START_MEMORY') OR define('KAZINDUZI_START_MEMORY', memory_get_usage());

require_once 'loader.php';

/**
 * Set the include path for the whole kazinduzi
 */
set_include_path(
        get_include_path()
        . PS . KAZINDUZI_PATH
        . PS . APP_PATH . DS . 'configs'
        . PS . KAZINDUZI_PATH . DS . 'includes'
        . PS . CONTROLLERS_PATH
        . PS . VIEWS_PATH
        . PS . LIB_PATH
        . PS . CORE_PATH
        . PS . APP_PATH
        . PS . DB_PATH
        . PS . WIDGETS_PATH
        . PS . KAZINDUZI_PATH . DS . 'helpers'
        . PS . KAZINDUZI_PATH . DS . 'elements'
        . PS . KAZINDUZI_PATH . DS . 'html'
);

require_once 'Kazinduzi.php';
require_once 'init.php';
require_once 'common_functions.php';

if (file_exists('install.php')) {
    require_once 'install.php';
}

$session = Kazinduzi::session();
$session->start();
require_once APP_PATH . DIRECTORY_SEPARATOR . 'bootstrap.php';
