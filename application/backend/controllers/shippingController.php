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
        $this->carriers = (new Carrier())->findAll();
    }
    
    public function handling()
    {
        $this->title = __('Shipping handling');
        $this->getTemplate()->setFilename('shipping/handling');
    }
    
    public function editCarrier()
    {
        $id = $this->getArg();
        $this->title = __('Carrier');
        $this->getTemplate()->setFilename('shipping/edit_carrier');
        $this->carrier = $carrier = new Carrier($id);
        if ($this->getRequest()->isPost()) {
            try{
                $carrier->name = $_POST['carrier']['name'];
                $carrier->shipping_method = $_POST['carrier']['shipping_method'];
                $carrier->setActive($_POST['carrier']['active']);
                $carrier->deleted = 0;
                $carrier->max_width = 0;
                $carrier->max_height = 0;
                $carrier->max_depth = 0;
                $carrier->max_weight = 0.00;
                $carrier->save();
                if ('stay' === $this->getRequest()->postParam('save_mode')) {
		    $this->redirect('/admin/shipping/edit_carrier/'.$carrier->getId());
		} else {
		    $this->redirect('/admin/shipping/carriers');
		}
            } catch (Exception $e){
                print_r($e);
            }
            
        }
    }
    
    public function editHandling()
    {
        $this->title = __('Handling');
        $this->getTemplate()->setFilename('shipping/edit_handling');
        $this->handling = new Handling();
    }
    
}

