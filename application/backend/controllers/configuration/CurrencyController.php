<?php

use library\Currency;

class ConfigurationCurrencyController extends Admin_controller
{

    public function __construct(\Request $req = null, \Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
        $currency = new Currency();
        $this->Template->setFilename('configuration/currencies');
        $this->title = __('configuration');
        $this->currencies = $currency->findAll();
    }
    
    /**
     * Add currency
     */
    public function add() 
    {
        $this->title = __('Add new cuurency');
        if ($this->Request->isPost()) {
            $data = $this->Request->postParam('currency_creation');
            $currency = new models\Currency();
            $currency->code = $data['code'];
            $currency->symbol = $data['symbol'];
            $currency->rate = $data['rate'];
            $currency->default = 0;            
            $currency->save();
            $this->redirect('/admin/configuration/currency');
        }
    }
    
    /**
     * Add currency
     */
    public function edit() 
    {
        $this->Template->setFilename('configuration/edit_currency');
        $id = $this->Request->getParam('currency_id');
        $currency = new models\Currency($id);
        $this->title = __('Edit new cuurency');
        $this->currency = $currency;
        if ($this->Request->isPost()) {
            $data = $this->Request->postParam('currency_creation');            
            $currency->code = $data['code'];
            $currency->symbol = $data['symbol'];
            $currency->rate = $data['rate'];
            $currency->default = 0;            
            $currency->save();
            $this->redirect('/admin/configuration/currency');
        }
    }

}
