<?php

/**
 * Description of StoreController
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
class ConfigurationStoreController extends Admin_controller
{
    /**
     * 
     * @param \Request $req
     * @param \Response $res
     */
    public function __construct(\Request $req = null, \Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
        $this->token = Security::token();
    }
    
    /**
     * 
     */
    public function index()
    {
        $allStores = models\Store::find('store');
        $this->Template->setFilename('configuration/store');
        $this->title = __('store');        
        $this->store = $allStores[0];
        $this->token = Security::token();
        if ($this->Request->isPost() && Security::check($this->Request->postParam('_token'))) {
            $data = $this->Request->postParam('store');
            $store = new models\Store($data['id']);
            $store->name = $data['name'];
            $store->description = $data['description'];
            $store->email = $data['email'];
            $store->address = $data['address'];
            $store->zipcode = $data['zipcode'];
            $store->city = $data['city'];
            $store->country = $data['country'];
            $store->phone = $data['phone'];
            $store->save();
            $this->redirect('/admin/configuration/store/?_store=' . $store->getId());
        }
    }
}
