<?php

// ensure we get report on all possible php errors
error_reporting(-1);

require __DIR__ . '/defines.php';
require KAZINDUZI_PATH . '/loader.php';
require KAZINDUZI_PATH . '/includes/init.php';
require KAZINDUZI_PATH . '/includes/common_functions.php';

# Register test classes
spl_autoload_register(function ($class) {
    $prefix = 'Kazinduzi\\Tests\\';
    $base_dir = __DIR__;
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});