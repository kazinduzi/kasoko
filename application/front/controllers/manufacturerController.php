<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * @author Emmanuel_Leonie
 */
class ManufacturerController extends BaseController
{

    public function __construct(Request $req = null, Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('php');
    }

    public function index()
    {
        
    }

    public function viewProducts()
    {
        $type = $this->getRequest()->getParam('type');
        $limit = $this->getRequest()->getParam('limit');
        $limit = !empty($limit) ? $limit : 8;
        $order = $this->getRequest()->getParam('order');
        $page = $this->getRequest()->getParam('page');
        $page = !empty($page) ? $page : 0;

        $slug = $this->getArg();
        $manufacturerProxy = new \models\Manufacturer\Manufacturer();
        $manufacturers = $manufacturerProxy->getBySlug($slug);
        $manufacturer = $manufacturers[0];

        $template = $this->getTemplate();
        $template->setFilename('manufacturer/view_products');
        $template->title = 'Manufacturer\'s products';
        $template->manufacturer = $manufacturer;
        $template->products = $products = $manufacturer->getProducts();
        $template->offset = $page * $limit;
        $template->limit = $limit;
        $template->order = $order;
        $template->type = $type;
        $template->limitOptions = array(
            '4' => 4,
            '8' => 8,
            '12' => 12,
            '50' => 50,
            'All' => 100000
        );
        $template->sortOptions = array(
            \Category::SORT_ALPHA => __('Name A to Z'),
            \Category::SORT_ALPHA_REV => __('Name Z to A'),
            \Category::SORT_PRICE_MIN => __('Price Lowest'),
            \Category::SORT_PRICE_MAX => __('Price Highest')
        );

        // Pagination
        $paginateTemplate = new Template('manufacturer/pagination', 'phtml');
        $paginateTemplate->manufacturer = $manufacturer;
        $paginateTemplate->total = iterator_count($products);
        $paginateTemplate->page = $page;
        $paginateTemplate->limit = $limit;
        $paginateTemplate->order = $order;
        $template->paginationHtml = $paginateTemplate->render();
    }

}
