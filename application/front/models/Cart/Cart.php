<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 11-7-2016
 * Time: 0:06
 */

namespace models\Cart;

class Cart extends \Model
{

    protected $table = 'cart';

    public function addItem($id, $name = null, $qty = null, $price = null, array $attributes = [])
    {
        
    }

    public function updateItem($id, $name = null, $qty = null, $price = null, array $attributes = [])
    {
        
    }

    public function getCartProducts()
    {
        return $this->cart_product;
    }

    public function save()
    {
        if (!$this->values['secure_token']) {
            $this->set('secure_token', bin2hex(openssl_random_pseudo_bytes(16)));
        }
        return parent::save();
    }

}
