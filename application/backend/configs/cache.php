<?php

defined('KAZINDUZI_PATH') || exit('No direct script access allowed');

/* Memcache configuration */
/*
  $config['driver'] = 'memcache';
  $config['servers'] = array(
  array(
  'host' => '127.0.0.1',
  'port' => 11211,
  'persistent' => FALSE,
  'weight' => 1,
  'timeout' => 1,
  'retry_interval' => 15,
  'status' => TRUE,
  'failure_callback' => NULL
  ),
  # Add another server here, if you have another one
  );
  $config['compression'] = FALSE;
  $config['compatibility'] = FALSE;
 */
/* APC cache configuration */
$config['driver'] = 'apc';

/* Redis cache configuration */
#$config['driver'] = 'Redis';

/* XCache cache configuration */
#$config['driver'] = 'Xcache';

/** Caching with file storage * */
/**
 * 
  $config['driver'] = 'file';
  $config['cache_dir'] = APP_PATH . DIRECTORY_SEPARATOR . 'cache';
  $config['ttl'] = 1800;
  $config['requests'] = 1000;
 *
 */
return $config;
