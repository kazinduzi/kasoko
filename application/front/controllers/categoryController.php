<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * @author Emmanuel_Leonie
 */
class CategoryController extends BaseController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->getTemplate()->setViewSuffix('phtml');
    }

    /**
     * Index action
     * 
     * @throws Exception
     */
    public function index()
    {
        $template = $this->getTemplate();
        $template->title = 'Kasoko Ecommerce Software, Kasoko Online Shop';
        try {
            $this->categories = Category::getInstance()->findAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * View action
     */
    public function view()
    {
        $category = Category::getByName($this->getArg());
        $type = $this->getRequest()->getParam('type');
        $limit = $this->getRequest()->getParam('limit');
        $limit = !empty($limit) ? $limit : 8;
        $order = $this->getRequest()->getParam('order');
        $page = $this->getRequest()->getParam('page');
        $page = !empty($page) ? $page : 0;
        
        #
        $template = $this->getTemplate();
        $template->meta_keywords = $category->meta_keyword;
        $template->meta_description = $category->meta_description;
        $template->category = $category;
        $template->products = $products = $category->getProducts(['limit' => $limit, 'order' => $order, 'type' => $type]);
        $template->limit = $limit;
        $template->order = $order;
        $template->page = $page;
        $template->offset = $page * $limit;
        $template->type = $type;
        $template->limitOptions = [
            '4' => 4,
            '8' => 8,
            '12' => 12,
            '50' => 50,
            'All' => 100000
        ];
        $template->sortOptions = [
            \Category::SORT_ALPHA => __('Name A to Z'),
            \Category::SORT_ALPHA_REV => __('Name Z to A'),
            \Category::SORT_PRICE_MIN => __('Price Lowest'),
            \Category::SORT_PRICE_MAX => __('Price Highest')
        ];

        // Pagination
        $paginateTemplate = new Template('category/pagination', 'phtml');
        $paginateTemplate->total = iterator_count($products);
        $paginateTemplate->page = $page;
        $paginateTemplate->limit = $limit;
        $paginateTemplate->order = $order;
        $paginateTemplate->categoryUrl = '/category/view/' . \Helpers\Stringify::slugify($category->name);
        $template->paginationHtml = $paginateTemplate->render();
    }

}
