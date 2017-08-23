<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LanguagesController
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
class ConfigurationLanguagesController extends Admin_controller
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
        
    }

}
