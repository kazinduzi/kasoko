<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Order
{

    protected static $db;

    /**
     * create an order
     * @param array $data
     * @return int
     */
    public static function create(array $data)
    {
        static::$db = Kazinduzi::db()->clear();
        try {
            $order_qry = "INSERT INTO `order` SET
                `customer_id` = " . (int) $data['customer_id'] . ",
                `amount` = '" . number_format($data['grand_total'], 2) . "',
                `payment_method` = '" . ($data['payment_method']) . "',
                `shipping_method` = '" . ($data['shipping_method']) . "',
                `shipping_name` = '" . ($data['shipping_name']) . "',
                `shipping_address` = '" . ($data['shipping_address']) . "',
                `shipping_address_2` = '" . ($data['shipping_address_2']) . "',
                `shipping_zipcode` = '" . ($data['shipping_zipcode']) . "',
                `shipping_city` = '" . ($data['shipping_city']) . "',
                `shipping_country` = '" . ($data['shipping_country']) . "',
                `shipping_zone` = '" . ($data['shipping_zone']) . "',
                `added_date` = NOW();";
            static::$db->query($order_qry);
            $orderId = static::$db->insert_id();

            // Add order_details
            foreach ($data['products'] as $product) {
                $order_detail_qry = "INSERT INTO `order_detail` SET `order_id` = " . (int) $orderId . ", `product_id` = " . (int) $product['product_id'] . ", `product_name` = '" . ($product['name']) . "', `product_sku` = '" . ($product['sku']) . "', `product_price` = '" . $product['price'] . "', `product_quantity` = '" . $product['quantity'] . "', `product_total` = '" . $product['total'] . "', `product_tax` = '" . $product['tax'] . "';";
                static::$db->query($order_detail_qry);
            }
            return $orderId;
        } catch (Exception $e) {
            print_r($e);
        }
    }

    /**
     *
     * @param type $orderId
     */
    public static function getById($orderId)
    {
        
    }

    /**
     *
     * @param type $customer_id
     */
    public static function getByCustomer($customer_id)
    {
        
    }

}
