<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

sanitize_input();

require CORE_PATH . DIRECTORY_SEPARATOR . 'dispatcher.class.php';
$dispatcher = new Dispatcher();
$dispatcher->dispatch();
