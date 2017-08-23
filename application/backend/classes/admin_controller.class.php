<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

abstract class Admin_controller extends Controller
{

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Admin_controller constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->auth = new Auth();
        $this->auth->is_logged_in($request->serverParam('REQUEST_URI'));
        $this->token = \Security::token();
        $this->setLayout('admin/layout');
        $this->disableCache();
        if (false === $this->getUser()->isActive()) {
            $this->auth->logout();
            $this->redirect('/login');
        }
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

    /**
     * @return Cache
     */
    public function getCache()
    {
        if (is_null($this->cache)) {
            $this->cache = Kazinduzi::cache();
        }
        return $this->cache;
    }

    /**
     * @return null|User
     */
    public function getUser()
    {
        return $this->auth->getUser();
    }

    public function getSession()
    {
        return $this->auth->getSession();
    }

}
