<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * @author Emmanuel_Leonie
 */
class CartController extends BaseController
{
    protected $cart;
    
    public function __construct(\Request $req = null, \Response $res = null)
    {
        header('X-Robots-Tag: noindex, nofollow', true);
        parent::__construct($req, $res);
        $this->cart = new Cart;
    }

    /**
     *
     */
    public function index()
    {
	$this->Template->setViewSuffix('phtml');
	$this->Template->Cart = $this->cart;
    }

    /**
     *
     * @throws Exception
     */
    public function add()
    {
	if (FALSE === filter_var($this->Request->postParam('product_id'), FILTER_VALIDATE_INT)) {
	    throw new Exception('Invalid product id');
	}        
	if (!empty($_POST['options'])) {
	    $key = (int)$this->Request->postParam('product_id') . ':' . base64_encode(serialize($this->Request->postParam['options']));
	} else {
	    $key = (int)$this->Request->postParam('product_id');
	}	
        $product = new Product($this->Request->postParam('product_id'));
        if ($product->quantity - $this->Request->postParam('quantity') < 0) {
            throw new Exception(sprintf(__('The selected quantity is greater than the actual product\'s quantity [%s]'), $product->getName()));
        }
        // Add to cart
	$cart = $this->cart->add($key, (int)$this->Request->postParam('quantity'));
	if ($cart instanceof Cart) {
	    echo json_encode(
		    array(
			'saved' => true,
			'count' => $cart->getCountItems()
		    )
	    );
	} else {
	    echo json_encode(array('saved' => false));
	}
	exit();
    }

    /**
     *
     * @throws Exception
     */
    public function update()
    {
	if (empty($quantity = $this->Request->postParam('quantity'))) {
	    throw new Exception('Invalid quantity');
	}
	$cart = new Cart();
	foreach ($quantity as $key => $qty) {
	    try {
		$cart->update($key, $qty);
	    } catch (Exception $e) {
		print_r($e);
	    }
	}
	redirect('/cart');
    }

    /**
     *
     * @throws Exception
     */
    public function remove()
    {
	if (!is_numeric($this->Request->postParam('product_id'))) {
	    throw new Exception('Invalid product id');
	}
	$cart = new Cart();
	$cart->remove($this->Request->postParam('product_id'));
	echo json_encode($cart->getCountItems());
	die;
    }

}
