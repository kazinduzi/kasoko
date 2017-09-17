<?php

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

/**
 *  nullify any existing autoloads
 */
function autoloader_core($className)
{
    if (!is_file($core_file = strtolower(CORE_PATH . DS . $className . CLASS_EXT))) {
        return;
    }
    require_once $core_file;
}

function autoloader_model($className)
{
    $camelcase_class = strtolower(preg_replace('/([a-z])([A-Z])/', '$1/$2', $className));
    if (!is_file($model_file = strtolower(MODELS_PATH . DS . $camelcase_class . MODEL_EXT))) {
        return;
    }
    require_once $model_file;
}

function autoloader_library($className)
{
    $camelcase_class = strtolower(preg_replace('/([a-z])([A-Z])/', '$1/$2', $className));
    if (!is_file($lib_file = strtolower(LIB_PATH . DS . $camelcase_class . CLASS_EXT))) {
        return;
    }
    require_once $lib_file;
}

function autoloader_helpers($className)
{
    $camelcase_class = strtolower(preg_replace('/([a-z])([A-Z])/', '$1/$2', $className));
    if (!is_file($helper_file = strtolower(KAZINDUZI_PATH . DS . 'helpers' . DS . $camelcase_class . CLASS_EXT))) {
        return;
    }
    require_once $helper_file;
}

function autoloader_classes($className)
{
    $camelcase_class = strtolower(preg_replace('/([a-z])([A-Z])/', '$1/$2', $className));
    if (!is_file($class_file = strtolower(APP_PATH . DS . 'classes' . DS . $camelcase_class . CLASS_EXT))) {
        return;
    }
    require_once $class_file;
}

function autoloader_db($className)
{
    if (!is_file($db_file = strtolower(DB_PATH . DS . $className . EXT))) {
        return false;
    }
    require_once $db_file;
}

function autoloader_controller($className)
{
    $className = preg_replace('/Controller$/', '', $className);
    $camelcase_class = strtolower(preg_replace('/([a-z])([A-Z])/', '$1/$2', $className));
    $controller_path = CONTROLLERS_PATH . DS . ($camelcase_class) . 'Controller.php';
    if (!is_file($controller_path)) {
        return false;
    }
    require_once $controller_path;
}

function autoloader_module($className)
{
    $file = MODULES_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

function autoloader_plugin($className)
{
    $file = PLUGINS_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

function autoloader_psr0($className)
{
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';
    if (false !== $lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    require $fileName;
}

spl_autoload_register(null, false);
spl_autoload_extensions('.php');
spl_autoload_register('autoloader_core');
spl_autoload_register('autoloader_model');
spl_autoload_register('autoloader_library');
spl_autoload_register('autoloader_db');
spl_autoload_register('autoloader_classes');
spl_autoload_register('autoloader_helpers');
spl_autoload_register('autoloader_controller');
spl_autoload_register('autoloader_module');
spl_autoload_register('autoloader_plugin');
spl_autoload_register('autoloader_psr0');
ini_set('unserialize_callback_func', 'spl_autoload_call');
