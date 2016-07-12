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

    public $hasMany = [
        'cart_product' => [
            'model' => '\\models\\Cart\CartProduct',
            'foreign_key' => 'cart_id',
        ]
    ];

    public function getCartProducts()
    {
        return $this->cart_product;
    }

    public function save()
    {
        if (!($this->secure_token)) {
            $this->set('secure_token', bin2hex(openssl_random_pseudo_bytes(16)));
        }
        return parent::save();
    }

}