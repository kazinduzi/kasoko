<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 19-7-2016
 * Time: 0:03
 */

namespace library\Cart;

use Kazinduzi;

class CartService
{

    protected $dbo;

    /**
     * @return Cart
     * @throws \Exception
     */
    public function createSessionCart()
    {
        $cart = new Cart(Kazinduzi::session());
        $cart->setCreatedTime(new \DateTime());
        $cartId = $cart->persist();
        $cart->setId($cartId);
        if (Kazinduzi::session()->contains('cart.id')) {
            Kazinduzi::session()->remove('cart.id');
        }
        Kazinduzi::session()->add('cart.id', $cartId);
        return $cart;
    }

    /**
     * @return Cart
     * @throws \Exception
     */
    public function getSessionCart()
    {
        if (!Kazinduzi::session()->contains('cart.id')) {
            $cart = $this->createSessionCart();
        } else {
            $cart = $this->findById(Kazinduzi::session()->contains('cart.id'));
            if (!$cart) {
                $cart = $this->createSessionCart();
            }
        }
        return $cart;
    }

    public function getDbo()
    {
        if (!$this->dbo instanceof \Datebase) {
            $this->dbo = Kazinduzi::db()->clear();
        }
        return $this->dbo;
    }

    /**
     * @param $cartId
     * @return Cart
     */
    public function findById($cartId)
    {
        $this->getDbo()->setQuery(sprintf("SELECT * FROM cart WHERE id = '%s'", $cartId));

        if (null !== $row = $this->getDbo()->fetchObjectRow()) {
            $cart = new Cart(Kazinduzi::session(), $row->id);
            $cart->setCreatedTime((new \DateTime)->setTimestamp($row->created_at));
            return $cart;
        }
        return null;
    }

}
