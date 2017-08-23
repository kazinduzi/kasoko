<?php



use library\Image\Editor as ImageEditor;
use models\Manufacturer\Manufacturer;

class OrdersController extends Admin_controller
{

    public function __construct(Request $req, Response $res)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
        $this->title = 'Order history';
        $this->Template->setFilename('order/overview');
        $orderContainer = new Order();
        $this->orders = $orderContainer->findAll();
    }

}
