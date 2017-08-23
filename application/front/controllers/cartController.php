<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * @author Emmanuel_Leonie
 */
class CartController extends BaseController
{

    /**
     *
     */
    public function index()
    {
        $cartService = new \library\Cart\CartService();
		if (empty($_COOKIE['cart_id'])) {
            $cart = $cartService->createSessionCart();
            $cart->add([
                ['id' => '293ad', 'name' => 'Product 1', 'qty' => 1, 'price' => 10.00],
                ['id' => '4832k', 'name' => 'Product 2', 'qty' => 5, 'price' => 15.00, 'options' => ['size' => 'large']]
            ]);
			$cookie = new \library\Cookie\Cookie('cart_id');
            $cookie->setValue($cart->getId());
            $cookie->setMaxAge(60 * 60 * 24 * 7); // Week
            // $cookie->setExpiryTime(time() + 60 * 60 * 24);
            // $cookie->setPath('/~rasmus/');
            // $cookie->setDomain('example.com');
            $cookie->setHttpOnly(true);
            //$cookie->setSecureOnly(true);
            //$cookie->setSameSiteRestriction('Strict');
            // echo $cookie;
            $cookie->save();
        }        
        print_r($_SESSION);
        print_r($_COOKIE);
        
        $this->Template->setViewSuffix('phtml');
        $this->Template->Cart = new Cart();
        
        #
//        $cart = new \models\Cart\Cart(1);
//        foreach($cart->getCartProducts() as $cartProduct) {
//            var_dump($cartProduct->getProduct()->getId());
//        }
    }

    /**
     * Add action
     * 
     * @throws Exception
     */
    public function add()
    {
        if (! is_numeric($this->Request->postParam('product_id'))) {
            throw new Exception('Invalid product id');
        }
        
        if ($this->Request->postParam('options')) {
            $key = $this->Request->postParam('product_id') . ':' . base64_encode(serialize($this->Request->postParam('options')));
        } else {
            $key = $this->Request->postParam('product_id');
        }
        
        // Create the cart
        $cart = new Cart();
        $result = $cart->add($key, $this->Request->postParam('quantity'));
        if ($result instanceof Cart) {
            print json_encode([
                    'saved' => true, 
                    'count' => $result->getCountItems()
                ]);
        } else {
            print json_encode(['saved' => false]);
        }
        exit();
    }

    /**
     * Update action
     * 
     * @throws Exception
     */
    public function update()
    {
        if (!($this->Request->postParam('quantity'))) {
            throw new Exception('Invalid quantity');
        }
        $cart = new Cart();
        foreach ($this->Request->postParam('quantity') as $key => $qty) {
            try {
                $cart->update($key, $qty);
            } catch (Exception $e) {
                print_r($e);
            }
        }
        redirect('/cart');
    }

    /**
     * Remove action
     * 
     * @throws Exception
     */
    public function remove()
    {
        if (!($this->Request->postParam('product_id'))) {
            throw new Exception('Invalid product id');
        }
        $cart = new Cart();
        $cart->remove($this->Request->postParam('product_id'));
        print json_encode($cart->getCountItems());
        exit();
    }

}
