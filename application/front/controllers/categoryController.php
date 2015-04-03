<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * @author Emmanuel_Leonie
 */
class CategoryController extends BaseController
{

    public function __construct()
    {
	parent::__construct();
	$this->getTemplate()->setViewSuffix('phtml');
    }

    public function index()
    {
	$template = $this->getTemplate();
	$template->title = "Category controller";
	try {
	    $this->categories = Category::getInstance()->findAll();
	} catch (Exception $e) {
	    throw $e;
	}
    }

    public function view()
    {
	$category = Category::getByName($this->getArg());
	$type = $this->getRequest()->getParam('type');
	$limit = $this->getRequest()->getParam('limit');
	$limit = !empty($limit) ? $limit : 8;
	//
	$template = $this->getTemplate();
	$template->meta_keywords = $category->meta_keyword;
	$template->meta_description = $category->meta_description;
	$template->category = $category;
	$template->products = null !== $category->getProducts() ? array_slice($category->getProducts(), 0, $limit) : array();
	$template->limit = $limit;
	$template->limitOptions = array('4' => 4, '8' => 8, '12' => 12, '50' => 50, 'All' => 100000);
    }

}
