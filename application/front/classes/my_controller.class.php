<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

abstract class My_controller extends Controller
{

    protected $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->auth->is_logged_in('/admin/');
        $this->setLayout('admin/default_admin');
        $this->disableCache();
    }

    public function init()
    {
        parent::init();
        if (isset($_POST['lang'])) {
            Kazinduzi::config()->set('lang', $_POST['lang']);
            Kazinduzi::session()->set('lang', $_POST['lang']);
            Kazinduzi::$language = $_POST['lang'];
        } elseif (Kazinduzi::session()->get('lang')) {
            Kazinduzi::$language = Kazinduzi::session()->get('lang');
        }
        return true;
    }

}
