<?php

class ConfigurationController extends Admin_controller
{

    public function __construct(\Request $req = null, \Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
        $this->getTemplate()->setFilename('configuration/index');
        $this->title = __('configuration');
    }

}
