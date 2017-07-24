<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 19-7-2016
 * Time: 0:03
 */

namespace library\Cart;


class CartService
{
    protected $session;
    protected $dbo;

    public function __construct()
    {
        $this->session = \Kazinduzi::session();
    }

    /**
     * @return Cart
     * @throws \Exception
     */
    public function createSessionCart()
    {
        $cart = new Cart($this->session);
        $cart->setCreatedTime(new \DateTime());
        $cartId = $cart->persist();
        $cart->setId($cartId);
        if ($this->session->contains('cart.id')) {
            $this->session->remove('cart.id');
        }
        $this->session->add('cart.id', $cartId);
        return $cart;
    }

    /**
     * @return Cart
     * @throws \Exception
     */
    public function getSessionCart()
    {
        if (! $this->session->contains('cart.id')) {
                $cart = $this->createSessionCart();
        } else {
            $cart = $this->findById($this->session->contains('cart.id'));
            if (! $cart) {
                $cart = $this->createSessionCart();
            }
        }
        return $cart;
    }

    public function getDbo()
    {
        if (! $this->dbo instanceof \Datebase) {
            $this->dbo = \Kazinduzi::db()->clear();
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
            $cart = new Cart($this->session, $row->id);
            $cart->setCreatedTime((new \DateTime)->setTimestamp($row->created_at));
            return $cart;
        }
        return null;
    }
}