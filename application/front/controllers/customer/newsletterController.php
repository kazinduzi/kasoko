<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class CustomerNewsletterController extends BaseController
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
    
    /**
     * Before evnet the controller
     */
    public function before()
    {
	if (!$this->getCustomer()->isLogged()) {
	    $this->redirect('/customer/login');
	}
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
    
    /**
     * IndexAction
     */
    public function index()
    {
	$this->Template->setViewSuffix('phtml');
	$this->Template->setFilename('account/newsletter_settings');
	$this->title = 'Newsletter';
	$this->Customer = new AccountCustomer($this->customer->getId());
	if ($this->getRequest()->isPost()) {	    
	    if (!empty($_POST['newsletter-subscribe'])) {
		$this->Customer->newsletter = 1;
		$this->Customer->save();
		$this->redirect('/customer/account');
	    } else {
		$this->Customer->newsletter = 0;
		$this->Customer->save();
	    }    
	}
    }
    
}