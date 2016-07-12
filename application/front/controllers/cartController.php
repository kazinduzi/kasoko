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
        $this->Template->setViewSuffix('phtml');
        $this->Template->Cart = new Cart();
        //
        $cart = new \models\Cart\Cart(1);
        foreach($cart->getCartProducts() as $cartProduct) {
            var_dump($cartProduct->getProduct()->getId());
        }

    }

    /**
     *
     * @throws Exception
     */
    public function add()
    {
        if (!is_numeric($this->Request->postParam('product_id'))) {
            throw new Exception('Invalid product id');
        }
        if (isset($_POST['options'])) {
            $key = (int)$_POST['product_id'] . ':' . base64_encode(serialize($_POST['options']));
        } else {
            $key = (int)$_POST['product_id'];
        }
        // Create the cart
        $cart = new Cart();
        $result = $cart->add($key, (int)$_POST['quantity']);
        if ($result instanceof Cart) {
            echo json_encode(
                array(
                    'saved' => true,
                    'count' => $result->getCountItems()
                )
            );
        } else {
            echo json_encode(array('saved' => false));
        }
        die;
    }

    /**
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
        echo json_encode($cart->getCountItems());
        die;
    }

}
