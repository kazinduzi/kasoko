<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Description of cart
 *
 * @author Emmanuel_Leonie
 */
class Cart
{

    private static $_instance;
    private $db = false;
    private $config = array();
    private $session = false;
    private $data = array();

    /**
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
        $this->db = \Kazinduzi::db();
        $this->config = \Kazinduzi::getConfig('session');
        $this->session = \Kazinduzi::session();
        if (!$this->session->get('cart') || !is_array($this->session->get('cart'))) {
            $this->session->add('cart', $this->data);
        }
    }

    /**
     * Create a singleton for the Customer
     * @return mixed
     */
    public static function getSingleton()
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     *
     * @param type $productid
     * @param type $qty
     * @param array $options
     * @return \Cart|boolean
     */
    public function add($productid, $qty = 1, array $options = null)
    {
        if (!$options) {
            $key = $productid;
        } else {
            $key = $productid . ':' . base64_encode(serialize($options));
        }
        if (is_numeric($qty) && ((int)$qty > 0)) {
            if (!array_key_exists($key, $__cart_data = $this->session->get('cart'))) {
                $__cart_data[$key] = (int)$qty;
            } else {
                $__cart_data[$key] += (int)$qty;
            }
            $this->session->add('cart', $__cart_data);
            return $this;
        }
        return false;
    }

    /**
     *
     * @param type $key
     * @param type $qty
     * @return \Cart
     */
    public function update($key, $qty)
    {
        if (!is_numeric($qty))
            throw new Exception('Invalid quantity provided', 1000);
        if ((int)$qty && ((int)$qty > 0) && array_key_exists($key, $data = $this->session->get('cart'))) {
            $data[$key] = (int)$qty;
            $this->session->add('cart', $data);
        } else {
            $this->remove($key);
        }
        return $this;
    }

    /**
     * Remove item from the cart
     * @param string $key
     * @return \Cart
     */
    public function remove($key)
    {
        if (array_key_exists($key, $data = $this->session->get('cart'))) {
            if (isset($data[$key])) {
                unset($data[$key]);
            }
            $this->session->add('cart', $data);
        }
        return $this;
    }

    /**
     * Alias for the clear method
     * @return boolean
     */
    public function destroy()
    {
        $this->clear();
        return true;
    }

    /**
     * Clear or destroy the cart
     * @return boolean
     */
    public function clear()
    {
        if ($this->session->get('cart')) {
            $this->session->remove('cart');
        }
        return true;
    }

    /**
     * @todo to be implemented for the sub-total
     */
    public function getSubTotal()
    {
        $sub_total = 0.0;
        if ($this->hasProducts()) {
            foreach ($this->session->get('cart') as $id => $qty) {
                $product_id = explode(':', $id);
                $Product = new Product($product_id[0]);
                $sub_total += $Product->price * (float)$qty;
            }
        }
        return $sub_total;
    }

    /**
     * Is there any products in the cart
     * @return bool
     */
    public function hasProducts()
    {
        return count($this->session->get('cart')) > 0;
    }

    /**
     * @todo
     */
    public function getTotal()
    {
        $total = 0.0;
        if ($this->hasProducts()) {
            foreach ($this->session->get('cart') as $id => $qty) {
                $product_id = explode(':', $id);
                $Product = new Product($product_id[0]);
                $total += $Product->price * (float)$qty * (100.0 + $Product->tax) / 100.0;
            }
        }
        return $total;
    }

    /**
     * Checks if
     * @return boolean
     */
    public function hasShipping()
    {
        static $shipping = false;
        foreach ($this->session->get('cart') as $id => $qty) {
            $product_id = explode(':', $id);
            $Product = new Product($product_id[0]);
            if ($Product->shipping) {
                $shipping = true;
                break;
            }
        }
        return $shipping;
    }

    /**
     * Get contents of the cart
     * @return array \Product
     * @throws Exception
     */
    public function getContent()
    {
        if (!$this->hasProducts())
            throw new Exception(1000, 'Cart is empty');
        return $this->session->get('cart');
    }

    /**
     * Get item counter of the cart
     * @return int
     * @throws Exception
     */
    public function getCountItems()
    {
        if (!$this->hasProducts())
            return 0;
        $count = 0;
        foreach ($this->session->get('cart') as $id => $qty) {
            $count += (int)$qty;
        }
        return $count;
    }

}
