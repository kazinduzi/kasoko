<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class CurrencyController extends BaseController
{

    protected $session;

    public function __construct(\Request $req, \Response $res)
    {
	parent::__construct($req, $res);
    }

    public function index()
    {
	$code = $this->getRequest()->getParam('code');
	if ($code !== $this->getSession()->get('currency')) {
	    $this->getSession()->add('currency', $code);
	}
	$this->redirect('/');
    }

}
