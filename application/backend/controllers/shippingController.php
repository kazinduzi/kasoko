<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

use models\Carrier\Carrier;

class ShippingController extends Admin_controller
{
    public function __construct(Request $req, Response $res)
    {
	parent::__construct($req, $res);
	$this->Template->setViewSuffix('phtml');
    }
    
    public function index()
    {
        $this->title = __('Shipping');
        $template = $this->getTemplate();
	$template->setFilename('shipping/index');
    }
    
    public function carriers()
    {
        $this->title = __('Shipping carriers');
        $this->getTemplate()->setFilename('shipping/carriers');
    }
    
    public function handling()
    {
        $this->title = __('Shipping handling');
        $this->getTemplate()->setFilename('shipping/handling');
    }
    
    public function editCarrier()
    {
        $this->title = __('Carrier');
        $this->getTemplate()->setFilename('shipping/edit_carrier');
        $this->carrier = new Carrier();
    }
    
    public function editHandling()
    {
        $this->title = __('Handling');
        $this->getTemplate()->setFilename('shipping/edit_handling');
        $this->handling = new Handling();
    }
    
}

