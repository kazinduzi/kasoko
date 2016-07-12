<?php

defined('KAZINDUZI_PATH') || exit('No direct script access allowed');

$config['driver'] = 'mysqli';
$config['persistent'] = FALSE; //TRUE;
$config['db_host'] = 'localhost';
$config['db_port'] = '3306';
$config['db_user'] = 'root';
$config['db_password'] = 'password';
$config['db_name'] = 'kazinduzi_kasoko';
$config['db_prefix'] = '';
$config['db_debug'] = TRUE;
$config['db_cache_on'] = FALSE;
$config['db_cache_dir'] = FALSE;
$config['db_auto_init'] = TRUE;
$config['db_auto_shutdown'] = TRUE;
$config['db_strict_on'] = FALSE;
return (array)$config;
