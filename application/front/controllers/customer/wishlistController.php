<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class CustomerWishlistController extends BaseController
{

    protected $customer;

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
     * Before evnet the controller
     */
    public function before()
    {
        if (!$this->getCustomer()->isLogged()) {
            $this->redirect('/customer/login');
        }
    }

    /**
     * Get the customer object
     *
     * @return \Customer
     */
    protected function getCustomer()
    {
        if (!$this->customer instanceof \Customer) {
            $this->customer = \Customer::getSingleton();
        }
        return $this->customer;
    }

    /**
     * indexAction for the wishlist
     */
    public function index()
    {
        $this->getTemplate()->setFilename('account/wishlist');
        $this->title = 'My Wishlist';
        $wishlist = $this->getSession()->get('wishlist');
        if (!empty($wishlist)) {
            $products = array();
            foreach ($wishlist as $productId) {
                $products[] = new Product((int)$productId);
            }
            $this->getTemplate()->set('wishlistProducts', $products);
        }
    }

    /**
     * Add product to the wishlist
     *
     * @throws \Exception
     */
    public function add()
    {
        if (!is_numeric($this->getRequest()->postParam('product_id'))) {
            throw new \Exception('Invalid product id');
        }
        $productId = $this->Request->postParam('product_id');
        $wishlist = $this->getSession()->get('wishlist');
        if (!in_array($productId, $wishlist)) {
            $wishlist[] = $productId;
            $this->getSession()->add('wishlist', $wishlist);
            echo json_encode(
                array(
                    'success' => true,
                    'total' => count($wishlist)
                )
            );
        }
        exit();
    }

    /**
     * Remove product from the wishlist
     *
     * @throws \Exception
     */
    public function remove()
    {
        if (!is_numeric($this->getRequest()->postParam('product_id'))) {
            throw new \Exception('Invalid product id');
        }
        $productId = $this->Request->postParam('product_id');
        $wishlist = $this->getSession()->get('wishlist');
        if (false !== $key = array_search($productId, $wishlist)) {
            unset($wishlist[$key]);
            $this->getSession()->add('wishlist', $wishlist);
            echo json_encode(
                array(
                    'success' => true,
                    'total' => count($wishlist)
                )
            );
        }
        exit();
    }

}
