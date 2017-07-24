<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class IndexController extends Admin_controller 
{

    public function index() 
    {
        $template = $this->getTemplate();
        $template->setViewSuffix('phtml');
        $template->setFilename('index/index');
        $template->data = array(1,2,3,4,5);
        $this->title = 'Admin';
        $this->content = "Hello world";
    }
}