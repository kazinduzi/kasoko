<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class CustomerLoginController extends BaseController
{

    protected $customer;

    /**
     *
     * @param \Request $req
     * @param \Response $res
     */
    public function __construct(\Request $req = null, \Response $res = null)
    {
	parent::__construct($req, $res);
	$this->Template->setViewSuffix('phtml');	
    }
    
    public function index()
    {
	$this->login();
    }
    
    /**
     * Customer login action 
     */
    public function login()
    {
	if ($this->getCustomer()->isLogged()) {
	    $this->redirect('/customer/account/');
	}
	$this->title = 'Login';
	$this->Template->setViewSuffix('phtml');
	$this->Template->setFilename('account/login');
	if ($this->Request->isPost() && ($data = $this->Request->postParam('login'))) {
	    $username = $data['username'];
	    $password = $data['password'];
	    $this->getCustomer()->login($username, $password);
	    if ($this->getCustomer()->getId()) {
		$this->redirect('/customer/account/');
	    } else {
		$this->error = 'Failed to login, username and/or password are incorrect';
	    }
	}
    }

    /**
     * Customer logout action
     */
    public function logout()
    {
	if (!$this->getCustomer()->isLogged()) {
	    return false;
	}
	$this->getCustomer()->logout();
	$this->title = 'Logout';
	$this->redirect('/');
    }
    
    /**
     * Get customer singleton object
     * @return Customer object
     */
    protected function getCustomer()
    {
	if (!$this->customer instanceof \Customer) {
	    $this->customer = \Customer::getSingleton();
	}
	return $this->customer;
    }
    
}