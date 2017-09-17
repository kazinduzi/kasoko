<?php

defined('KAZINDUZI_PATH') || exit('No direct script access allowed');

sanitize_input();

require CORE_PATH . '/dispatcher.class.php';
$dispatcher = new Dispatcher();
$dispatcher->dispatch();
