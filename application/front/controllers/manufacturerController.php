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
        $slug = $this->getArg();
        $manufacturerProxy = new \models\Manufacturer\Manufacturer();
        $manufacturers = $manufacturerProxy->getBySlug($slug);
        $manufacturer = $manufacturers[0];
        $type = $this->getRequest()->getParam('type');
        $limit = $this->getRequest()->getParam('limit');
        $limit = !empty($limit) ? $limit : 8;
        $template = $this->getTemplate();
        $template->title = 'Manufacturer\'s products';
        $template->manufacturer = $manufacturer;
        $template->products = $manufacturer->getProductsByLimit($limit);
        $template->limit = $limit;
        $template->limitOptions = array('4' => 4, '8' => 8, '12' => 12, '50' => 50, 'All' => 100000);
        $template->setFilename('manufacturer/view_products');
    }

}

?>