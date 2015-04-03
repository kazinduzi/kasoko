<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

abstract class Admin_controller extends Controller 
{
    protected $auth;

    /**
     * 
     */
    public function  __construct(Request $req, Response $res) 
    {   
        parent::__construct($req, $res);
        $this->auth = new Auth();
        $this->auth->is_logged_in(false);
        $this->setLayout('admin/layout');
        $this->disableCache();
        $this->token = \Security::token(); 
    }

    /**
     * 
     * @return boolean
     */
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
