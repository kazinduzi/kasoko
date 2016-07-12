<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10-7-2016
 * Time: 23:52
 */

namespace models\Cart;


use Model;

class CartProduct extends Model
{
    protected $table = 'cart_product';

    public $belongsTo = [
        'cart' => [
            'model' => '\\models\\Cart\Cart',
            'foreign_key' => 'cart_id',
        ]
    ];

    public $hasOne = [
        'product' => [
            'model' => '\\Product',
            'foreign_key' => 'product_id',
        ],
    ];

    public $hasMany = [
        'attribute' => [
            'model' => '\\models\\Attribute',
            'foreign_key' => 'attribute_id',
        ],
    ];

    public function getAttributes()
    {
        return $this->attribute;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
}