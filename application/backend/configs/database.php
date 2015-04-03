<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

$config = array(
    'driver' => 'mysqli',
    'persistent' => true,
    'db_host' => 'localhost',
    'db_port' => '3306',
    'db_user' => 'root',
    'db_password' => 'password',
    'db_name' => 'kazinduzi_kasoko',
    'db_prefix' => '',
    'db_debug' => false,
    'db_cache_on' => false,
    'db_auto_init' => true,
    'db_auto_shutdown' => true,
    'db_strict_on' => false,
);
