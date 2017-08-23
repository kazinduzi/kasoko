<?php

/**
 * Description of customersController
 *
 * @author Emmanuel_Leonie
 */
class customersController extends Admin_controller
{

    public function __construct(Request $req, Response $res)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
        $template = $this->getTemplate();
        $template->setFilename('customer/index');
        $template->title = __('Customers');
        $customer = new Customer;
        $template->customers = $customer->findAll();
        /**
         * 
          $x = $customer->findByEmail('endayiragije@yahoo.fr');
          var_dump($x[0]->values);
         *
         */
    }

}
