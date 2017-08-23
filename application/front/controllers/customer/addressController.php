<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class CustomerAddressController extends BaseController
{

    protected $accountCustomerModel;

    /**
     *
     * @param \Request $req
     * @param \Response $res
     */
    public function __construct(\Request $req = null, \Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
        $this->accountCustomerModel = new AccountCustomer(\Customer::getSingleton()->getId());
    }

    /**
     * Before evnet the controller
     */
    public function before()
    {
        
    }

    public function index()
    {
        $this->Template->setFilename('address/index');
        $this->title = 'Address';
        $this->Customer = $this->getCustomer();
        $this->shipping_address = $this->getCustomer()->getShippingAddress();
    }

    /**
     * Get customer singleton object
     * @return Customer object
     */
    protected function getCustomer()
    {
        return \Customer::getSingleton();
    }

    /**
     *
     */
    public function editBillingAddressPost()
    {
        if (!$this->getCustomer()->isLogged()) {
            $this->redirect('/customer/login');
        }
        if ($this->Request->isPost()) {
            $this->accountCustomerModel->editCustomerAddress($this->Request->postParams());
            $this->redirect('/customer/address');
        }
    }

    /**
     *
     */
    public function editBillingAddress()
    {
        if (!$this->getCustomer()->isLogged()) {
            $this->redirect('/customer/login');
        }
        $this->Template->setFilename('address/edit_billing_address');
        $this->title = 'Edit billing address';
        $this->Customer = $this->getCustomer();
        $this->countries = Country::getAll();
        if ($this->getCustomer()->getCountryId()) {
            $this->zones = Country::getZonesByCountryId($this->getCustomer()->getCountryId());
            $this->zone_id = Customer::getSingleton()->getZoneId();
        }
    }

    public function editShippingAddressPost()
    {
        if (!$this->getCustomer()->isLogged()) {
            $this->redirect('/customer/login');
        }
        if ($this->Request->isPost()) {
            $this->accountCustomerModel->editCustomerShippingAddress($this->Request->postParams());
            $this->redirect('/customer/address');
        }
    }

    public function editShippingAddress()
    {
        if (!$this->getCustomer()->isLogged()) {
            $this->redirect('/customer/login');
        }
        $this->Template->setFilename('address/edit_shipping_address');
        $this->title = 'Edit shipping address';
        $this->shipping_address = $this->getCustomer()->getShippingAddress();
        $this->countries = Country::getAll();
        if ($this->getCustomer()->getCountryId()) {
            $this->zones = Country::getZonesByCountryId($this->getCustomer()->getShippingAddress()->country_id);
            $this->zone_id = $this->getCustomer()->getShippingAddress()->zone_id;
        }
    }

    public function createShippingAddress()
    {
        $this->Template->setFilename('address/create_shipping_address');
        $this->title = 'New shipping address';
    }

    public function createShippingAddressPost()
    {
        if ($this->Request->isPost()) {
            $this->accountCustomerModel->addShippingAddress($this->Request->postParams());
            redirect('/customer/address/edit_shipping_address/');
        }
    }

    public function zone()
    {
        header('Content-Type: text/html; charset=UTF-8');
        echo Country::getZonesByCountry($this->getArg(0), $this->getArg(1));
        die;
    }

}
