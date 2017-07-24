<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class LoginController extends Controller
{

    const DEFAULT_REDIRECT_ADMIN_URI = '/admin';
    const DEFAULT_LOGIN_URI = '/login';

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * Constructor for loginController
     */
    public function __construct(Request $req = null, Response $res = null)
    {
        parent::__construct($req, $res);
        $this->auth = new Auth;
        $this->setLayout('admin/default_admin');
    }

    /**
     *
     */
    public function before()
    {
        $this->redirectUri = $this->auth->getSession()->get('redirect') ?: self::DEFAULT_REDIRECT_ADMIN_URI;
        return parent::before();
    }

    public function index()
    {
        if ($this->auth->is_logged_in(false, false)) {
            $this->redirect($this->redirectUri);
        }
        if ($this->Request->isPost()) {
            foreach ($_POST as $key => $value) {
                $options[$key] = $_POST[$key];
            }
            if (true === $this->auth->login($options)) {
                $this->redirect($this->redirectUri);
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
