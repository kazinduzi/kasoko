<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 11-7-2016
 * Time: 0:06
 */

namespace library\Cart;

use library\Escaper\Exception\RuntimeException;
use Session;

class Cart
{
    private $table = 'cart';
    private $session;
    private $dbo;
    private $id;
    private $secure_token;
    private $created_at;

    /**
     * Cart constructor.
     * @param Session $session
     * @param null $cartId
     */
    public function __construct(Session $session, $cartId = null)
    {
        $this->id = $cartId;
        $this->session = $session;
        $this->secure_token = bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * Magic method to make accessing the total, tax and subtotal properties possible.
     *
     * @param string $attribute
     * @return float|null
     */
    public function __get($attribute)
    {
        if ($attribute === 'total') {
            return $this->total(2, '.', '');
        }

        if ($attribute === 'tax') {
            return $this->tax(2, '.', '');
        }

        if ($attribute === 'subtotal') {
            return $this->subtotal(2, '.', '');
        }

        return null;
    }

    /**
     * @return \Database|\DbActiveRecord
     */
    public function getDbo()
    {
        if (!$this->dbo instanceof \Database) {
            $this->dbo = \Kazinduzi::db()->clear();
        }

        return $this->dbo;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * @param $cartId
     * @return $this
     */
    public function setId($cartId)
    {
        $this->id = $cartId;
        return $this;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    public function setCreatedTime(\DateTime $datetime)
    {
        $this->created_at = $datetime;
        return $this;
    }

    public function getCreatedime()
    {
        return $this->created_at;
    }

    /**
     * @param $id
     * @param null $name
     * @param null $qty
     * @param null $price
     * @param array $attributes
     * @return array
     */
    public function add($id, $name = null, $qty = null, $price = null, array $attributes = [])
    {
        if ($this->isMulti($id)) {
            return array_map(function ($item) {
                return $this->add($item);
            }, $id);
        }

        $cartItem = $this->createCartItem($id, $name, $qty, $price, $attributes);
        $cartContent = $this->getContent();
        if (isset($cartContent[$cartItem->rowId])) {
            $cartItem->qty += $cartContent[$cartItem->rowId]->qty;
        }

        $cartItems = $this->session->get('cart.items');
        $cartItems[$cartItem->rowId] = $cartItem;
        $this->session->set('cart.items', $cartItems);
        
        return $cartItem;
    }

    /**
     * @param $id
     * @param null $name
     * @param null $qty
     * @param null $price
     * @param array $attributes
     */
    public function update($id, $name = null, $qty = null, $price = null, array $attributes = [])
    {
        if ($this->isMulti($id)) {
            return array_map(function ($item) {
                return $this->add($item);
            }, $id);
        }

        $cartItem = $this->createCartItem($id, $name, $qty, $price, $attributes);
        $cartContent = $this->getContent();
        if (isset($cartContent[$cartItem->rowId])) {
            $cartItem->qty += $cartContent[$cartItem->rowId]->qty;
        }

        $cartItems = $this->session->get('cart.items');
        $cartItems[$cartItem->rowId] = $cartItem;
        $this->session->set('cart.items', $cartItems);
        
        return $cartItem;
    }

    /**
     * @return float|null
     */
    public function getCartProducts()
    {
        return $this->cart_product;
    }

    /**
     * Store an the current instance of the cart.
     *
     * @return \integer
     */
    public function persist()
    {
        $content = $this->getContent();
        return $this->getDbo()->insert($this->getTableName(), [
                    'secure_token' => $this->secure_token,
                    'content' => serialize($content),
                    'created_at' => ($this->created_at instanceof \DateTime ? $this->created_at->getTimestamp() : $this->created_at)
        ]);
    }

    /**
     * Get the content of the cart.
     *
     * @return array
     */
    public function content()
    {

        if (is_null($this->session->get($this->id))) {
            return [];
        }

        return $this->session->get($this->id);
    }

    /**
     * Check if the item is a multidimensional array or an array of Buyables.
     *
     * @param mixed $item
     * @return bool
     */
    private function isMulti($item)
    {
        if (!is_array($item)) {
            return false;
        }

        return is_array(current($item));
    }

    /**
     * @param $id
     * @param $name
     * @param $qty
     * @param $price
     * @param array $options
     * @return CartItem
     */
    private function createCartItem($id, $name, $qty, $price, array $options = [])
    {
        if (is_array($id)) {
            $cartItem = CartItem::fromArray($id);
            $cartItem->setQuantity($id['qty']);
        } else {
            $cartItem = CartItem::fromAttributes($id, $name, $price, $options);
            $cartItem->setQuantity($qty);
        }

        $cartItem->setTaxRate($cart_tax = 21);

        return $cartItem;
    }

    /**
     * Get the carts content, if there is no cart content set yet, return a new empty Collection
     *
     * @return array
     */
    protected function getContent()
    {
        if ($this->session->contains('cart.items')) {
            return $this->session->get('cart.items');
        }
    }

}
