<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

// Send Powered By Header.
header("X-Powered-By: Kazinduzi framework ver." . Kazinduzi::VERSION . ' (' . Kazinduzi::CODENAME . ')', false);
// Init the Application
Kazinduzi::init();