<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PluginsController
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
class ConfigurationPluginsController extends Admin_controller
{
    
    /**
     * Constructor
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
     * IndexAction
     */
    public function index()
    {
        $this->Template->setFilename('plugins/overview');
        $this->title = __('plugins overview');
        $allPlugins = \library\Plugin::getList();
        $allInstalledPlugins = \library\Plugin::getAllInstalled();
        $allActivePlugins = \library\Plugin::getAllActive();
        print_r($allPlugins);
        print_r($allActivePlugins);
        print_r($allInstalledPlugins);
    }

}
