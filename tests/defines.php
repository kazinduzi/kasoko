<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);
defined('EXT') || define('EXT', '.php');
defined('CLASS_EXT') || define('CLASS_EXT', '.class.php');
defined('MODEL_EXT') || define('MODEL_EXT', '.model.php');

/**
 *  define the site path
 */
defined('KAZINDUZI_PATH') || define('KAZINDUZI_PATH', __DIR__ . '/../');

defined('CORE_PATH') || define('CORE_PATH', (KAZINDUZI_PATH . '/core'));
defined('LIB_PATH') || define('LIB_PATH', (KAZINDUZI_PATH . '/library'));
defined('DB_PATH') || define('DB_PATH', (KAZINDUZI_PATH . '/database'));
defined('WIDGETS_PATH') || define('WIDGETS_PATH', (KAZINDUZI_PATH . '/widgets'));
defined('LAYOUT_PATH') || define('LAYOUT_PATH', (KAZINDUZI_PATH . '/elements/layouts'));
defined('APP_PATH') || define('APP_PATH', (KAZINDUZI_PATH . '/application/front'));
defined('MODULES_PATH') || define('MODULES_PATH', (KAZINDUZI_PATH . '/modules'));
defined('PLUGINS_PATH') || define('PLUGINS_PATH', (KAZINDUZI_PATH . '/plugins'));

defined('CONTROLLERS_PATH') || define('CONTROLLERS_PATH', (APP_PATH . '/controllers'));
defined('VIEWS_PATH') || define('VIEWS_PATH', (APP_PATH . '/views'));
defined('MODELS_PATH') || define('MODELS_PATH', (APP_PATH . '/models'));
defined('THEME_PATH') || define('THEME_PATH', (APP_PATH . '/theme'));

defined('CURRENT_URL') || define('CURRENT_URL', ((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'kasoko.docker') . ($_SERVER['REQUEST_URI'] ?? '/')  ));
defined('HOME_URL') || define('HOME_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'kasoko.docker'));
defined('SITE_URL') || define('SITE_URL', '/');
defined('KAZINDUZI_DEBUG') || define('KAZINDUZI_DEBUG', true);

/**
 * Define the start time of the application.
 */
defined('KAZINDUZI_START_TIME') || define('KAZINDUZI_START_TIME', microtime(true));

/**
 * Define the memory usage at the start of the application.
 */
defined('KAZINDUZI_START_MEMORY') || define('KAZINDUZI_START_MEMORY', memory_get_usage());