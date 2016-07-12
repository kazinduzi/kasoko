<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */

/**
 * Product model class
 */
class Order extends Model
{
    const TABLE_ORDER = 'order';
    const TABLE_ORDER_DETAIL = 'order_detail';
    const PRIMARY_KEY = 'order_id';

    protected $table = self::TABLE_ORDER;
    protected $pk = self::PRIMARY_KEY;
    protected $items = array();


    /**
     * Get orders by accountId
     *
     * @param integer $customerId
     * @return array
     */
    public function getByCustomer($customerId)
    {
        return $this->findAll('customer_id=' . (int)$customerId);
    }

    /**
     * Get order items
     *
     * @return array
     */
    public function getItems()
    {
        $sql = sprintf("SELECT * FROM " . self::TABLE_ORDER_DETAIL . " WHERE `order_id` = %d", $this->getId());
        $this->getDbo()->clear()->setQuery($sql);
        return $this->items = $this->getDbo()->fetchObjectList();
    }
}