<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class LoginController extends Controller
{

    const DEFAULT_REDIRECT_ADMIN_URI = '/admin';
    const DEFAULT_LOGIN_URI = '/login';

    protected $auth;

    /**
     * Constructor for loginController
     */
    public function __construct(Request $req = null, Response $res = null)
    {
        parent::__construct($req, $res);
        $this->auth = new Auth;
        $this->setLayout('admin/default_admin');
    }

    public function index()
    {
        $redirectUri = $this->auth->getSession()->get('redirect');
        $redirectUri = !empty($redirectUri) ? $redirectUri : self::DEFAULT_REDIRECT_ADMIN_URI;
        if ($this->auth->is_logged_in(false, false)) {
            $this->redirect($redirectUri);
        }
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $options[$key] = $_POST[$key];
            }
            if ($this->auth->login($options)) {
                $this->redirect($redirectUri);
            } else {
                $this->redirect(self::DEFAULT_LOGIN_URI);
            }
        }
        $this->title = 'Login Page';
        $this->resetPasswordLink = 'login/reset_password';
    }

    public function logout()
    {
        $this->auth->logout();
        redirect('/login');
    }

}
