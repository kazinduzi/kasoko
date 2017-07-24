<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use models\Manufacturer\Manufacturer;

class ManufacturersController extends Admin_controller
{

    public function __construct(Request $req, Response $res)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
        $manufacturer = new Manufacturer();
        $template = $this->getTemplate();
        $template->setFilename('manufacturer/list');
        $template->title = __('Our manufacturers');
        $template->activeManufacturers = $manufacturer->getAllActive();
        $template->allManufacturers = $manufacturer->getAll();
    }

    public function add($param)
    {
        $manufacturer = new Manufacturer();
        $template = $this->getTemplate();
        $template->setFilename('manufacturer/add');
        $template->title = __('Add manufacturer');
        $template->manufacturer = $manufacturer;
        if ($this->Request->isPost()) {
            try {
                $savemode = $_POST['save_mode'];
                $data = $_POST['manufacturer'];
                $manufacturer->name = $data['name'];
                $manufacturer->slug = \Stringify::slugify($data['name']);
                $manufacturer->setActive($data['active']);
                $manufacturer->save();
                'stay' === $savemode ? redirect('/admin/manufacturers/edit/' . $manufacturer->getId()) : redirect('/admin/manufacturers');
            } catch (Exception $ex) {
                print_r($ex);
            }
        }
    }

    public function edit()
    {
        $manufacturer = new Manufacturer($this->getArg());
        $template = $this->getTemplate();
        $template->setFilename('manufacturer/edit');
        $template->manufacturer = $manufacturer;
        $template->title = sprintf(__('Edit manufacturer') . ': %s', $manufacturer->name);
        if ($this->Request->isPost()) {
            try {
                $savemode = $_POST['save_mode'];
                $data = $_POST['manufacturer'];
                $manufacturer->name = $data['name'];
                $manufacturer->slug = \Stringify::slugify($data['name']);
                $manufacturer->setActive($data['active']);
                $manufacturer->save();
                'stay' === $savemode ? redirect('/admin/manufacturers/edit/' . $manufacturer->getId()) : redirect('/admin/manufacturers');
            } catch (Exception $ex) {
                print_r($ex);
            }
        }
    }

    public function view()
    {
        $manufacturer = new Manufacturer($this->getArg(0));
        $template = $this->getTemplate();
        $template->setFilename('manufacturer/view');
        $template->title = $manufacturer->name;
        $template->manufacturer = $manufacturer;
    }
    
    public function delete() 
    {        
        try{
            $manufacturer = new Manufacturer($this->getArg(0));
            $manufacturer->delete();
            $this->redirect('/admin/manufacturers');
        } catch (\Exception $e) {
            print_r($e);
        }
    }

}
