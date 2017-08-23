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
defined('APP_PATH') OR define('APP_PATH', realpath(KAZINDUZI_PATH . DS . 'application/front'));
defined('MODULES_PATH') OR define('MODULES_PATH', realpath(KAZINDUZI_PATH . '/modules'));
defined('PLUGINS_PATH') OR define('PLUGINS_PATH', realpath(KAZINDUZI_PATH . '/plugins'));

defined('CONTROLLERS_PATH') OR define('CONTROLLERS_PATH', realpath(APP_PATH . DS . 'controllers'));
defined('VIEWS_PATH') OR define('VIEWS_PATH', realpath(APP_PATH . DS . 'views'));
defined('MODELS_PATH') OR define('MODELS_PATH', realpath(APP_PATH . DS . 'models'));
defined('THEME_PATH') || define('THEME_PATH', realpath(APP_PATH . DIRECTORY_SEPARATOR . 'theme'));

defined('CURRENT_URL') OR define('CURRENT_URL', ((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
defined('HOME_URL') OR define('HOME_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
defined('SITE_URL') OR define('SITE_URL', '/');
defined('KAZINDUZI_DEBUG') OR define('KAZINDUZI_DEBUG', true);
/**
 * Define the start time of the application.
 */
defined('KAZINDUZI_START_TIME') OR define('KAZINDUZI_START_TIME', microtime(true));
/**
 * Define the memory usage at the start of the application.
 */
defined('KAZINDUZI_START_MEMORY') OR define('KAZINDUZI_START_MEMORY', memory_get_usage());

/**
 * Include first the automatic loader for classes and other
 */
require_once 'loader.php';

/**
 * Set the include path for the whole kazinduzi
 */
$includePaths = array(
    KAZINDUZI_PATH,
    APP_PATH . DS . 'configs',
    KAZINDUZI_PATH . DS . 'includes',
    CONTROLLERS_PATH,
    VIEWS_PATH,
    LIB_PATH,
    MODULES_PATH,
    PLUGINS_PATH,
    CORE_PATH,
    APP_PATH,
    DB_PATH,
    WIDGETS_PATH,
    KAZINDUZI_PATH . DS . 'helpers',
    KAZINDUZI_PATH . DS . 'elements',
    KAZINDUZI_PATH . DS . 'html',
    KAZINDUZI_PATH . DS . 'vendor'
);

array_push($includePaths, get_include_path());
set_include_path(join(PATH_SEPARATOR, $includePaths));

/**
 * include the init|functions|bootstrap  *
 */
require_once 'Kazinduzi.php';
require_once 'init.php';
require_once 'common_functions.php';
if (file_exists('install.php')) {
    require_once 'install.php';
}

/**
 * ! IMPORTANT NOTICE !
 * -----------------------------------------------------------------------------
 * Put this before bootstrapping. Because this will work session_start();
 * which is always on the top of the file.
 * Here we are using the custom session storage.
 * It is used as default storage OR database storage
 */
$session = Kazinduzi::session();
$session->start();
require_once APP_PATH . DIRECTORY_SEPARATOR . 'bootstrap.php';

/*
  $solrConfig = \Kazinduzi::getConfig('solr');
  $client = new Solarium\Client($solrConfig);
  var_dump($client);
 * 
 */

/*
  echo formatBytes(KAZINDUZI_START_MEMORY) . ' <=> ' . formatBytes(memory_get_usage()) . "\n";
  echo 'Executed in ', round(microtime(true) - KAZINDUZI_START_TIME, 2), 's';

  // Zend 2 XSS Protection
  use library\Escaper\Escaper as Escaper;

  $escaper = new Escaper('utf-8');
  var_dump($escaper->escapeHtml('<script>alert(\'Hello\')</script>'));

  echo __('Dutch');

  use library\Currency as Currency;

  $currency = Currency::getInstance()->getCurrent();
  var_dump($currency->code, $currency->getRate());

  echo $hash = password_hash("chispa", PASSWORD_BCRYPT, array("cost" => 10));

  $i18n = new I18n();
  echo $i18n->translate('messages.dutch');

  print_r(opcache_get_configuration());
 */

//Configuration::set('shop_name', 'Kasoko');
//Configuration::set('frontend_baseUrl', HOME_URL);

/**
 *
 * if ($_FILES && $_FILES['upload']) {
 * $image = new Image($_FILES['upload']['tmp_name']);
 * $image->resizeToWidth(100)->output(true);
 * }
 *
 * echo 'xxxxxxxxxxxxxxx Testing the Template class xxxxxxxxxxxxxxx';
 * try {
 * $Template = new Template('test', 'phtml');
 * $Template->name = 'FooBaz';
 * echo $Template->render();
 * }
 * catch(Exception $e){
 * throw $e;
 * }
 *
 * if ( function_exists('apache_request_headers') ) {
 * //$headers = apache_request_headers();
 * //print_r($headers);
 * }
 * //$_SESSION['token'] = sha1(uniqid(mt_rand(), true));
 * //$_SESSION['my_name'] = "Emmanuel Ndayiragije";
 * /*
 * $session->add('token', sha1(uniqid(mt_rand(), true)));
 * $session->add('my_name', 'Emmanuel ndayiragije');
 * $session->add('my_var1', 'Hello world');
 * $session->add('my_var2', 'Fooz Bar');
 * print_r($data = $session->toArray());
 * $session->remove('token');
 * print_r($data = $session->toArray());
 * echo $session->get('my_var1');
 * echo $session->id();
 * $parts = explode(DS, KAZINDUZI_PATH);
 * echo implode(DS, $parts);
 * print_r($parts);
 *
 * $x = $session->getIterator();
 * foreach($x as $k=>$v)
 * {
 * //var_dump($k, $v);
 * //echo "\n";
 * }
 *
 *
 * echo '<pre>';
 * $cart = new Cart();
 * $cart->add('1', 10, $options=array());
 * $cart->add('2', 10, $options=array('S','L','M', 'XL', 'XXL'));
 * //$cart->update('1', 10);
 * //$cart->add('1', 12);
 * //$cart->remove('1');
 * //$cart->clear();
 * //echo $cart->hasProducts();
 * print_r($_SESSION);
 * echo '</pre>';
 * echo $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
 * echo "<br/>" . URL::toAscii('TÃ¤nk efter - #' . ' PerchÃ© l\'erba Ã¨ verde? &');
 * echo '<p>';
 * echo String::capitalize('tÃ¤nk efter - #');
 * echo String::random(8);
 * echo '</p>';
 * echo '<br/>method: ',$ajax = get_request_method();
 *
 */
$params = $session->getCookieParams();
var_dump($params);
die;

$cookie = new \library\Cookie\Cookie('test-cookie');
$cookie->setDomain('kasoko.hp.kazinduzidev.com');
$cookie->setValue('dfsdfjasd;lfásjkdfáf!');
$cookie->setHttpOnly(true);
//$cookie->setSecure(true);
$cookie->save();

print_r($_COOKIE);
