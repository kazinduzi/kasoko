<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

$config['Application.name'] = 'Kazinduzi, a rapid development PHP web application Framework';

$config['debug'] = true;                 // true or false
$config['lang'] = 'en_US';
$config['charset'] = 'UTF-8';

$config['date.timezone'] = 'Europe/Amsterdam';

$config['default_action'] = 'index';     /* THIS IS A DEFAULT ACTION, IT MUST BE IMPLEMENTED IN ALL CONTROLLERS, MUST BE */
$config['default_controller'] = 'index'; /* THIS IS A CONTROLLER TO BE RUN ON DEFAULT, MUST BE */

return (array)$config;
