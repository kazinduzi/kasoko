<?php

class ConfigurationSettingsController extends Admin_controller
{

    public function __construct(\Request $req = null, \Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
        $this->getTemplate()->setFilename('configuration/settings');
        $this->title = 'Settings';
        if ($this->Request->isPost()) {
            Configuration::set('shop_logo', HOME_URL . '/_theme/default/front/images/logo.png');
            Configuration::set('shop_email', $this->getRequest()->postParam('shop_email'));
            Configuration::set('shop_name', $this->getRequest()->postParam('shop_name'));
            Configuration::set('frontend_baseUrl', $this->getRequest()->postParam('frontend_baseUrl'));
        }
    }

    public function save()
    {
        
    }

}
