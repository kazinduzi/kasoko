<?php

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */

namespace library\Product;

use Product;

/**
 * Description of ImageMapper
 *
 * @author Emmanuel_Leonie
 */
class ImageProvider
{

    protected $tableName = Image::PRODUCT_IMAGE_TABLE;
    protected $dbo;
    protected $product;

    /**
     * Constructor
     */
    public function __construct(Product $product = null)
    {
        $this->product = $product;
        if (!$this->dbo instanceof \Database) {
            $this->dbo = \Database::getInstance();
        }
    }

    /**
     * findById
     *
     * @param integer $id
     * @return \library\Product\Image|null
     */
    public function findById($id)
    {
        $this->dbo->setQuery(sprintf('select * from `%s` where `id`=%d and product_id=', $this->tableName, $id));
        if (null === $row = $this->dbo->fetchObjectRow()) {
            return null;
        }
        return new Image($row);
    }

    /**
     * findByProduct
     *
     * @param \Product $product
     * @return \library\Product\Image|null
     */
    public function findByProduct(\Product $product)
    {
        $data = array();
        $this->dbo->setQuery(sprintf('select * from `%s` where `product_id` = %d', $this->tableName, $product->getId()));
        if (null === $rows = $this->dbo->fetchObject()) {
            return null;
        }
        foreach ($rows as $row) {
            $data[] = new Image($row);
        }
        return $data;
    }

    /**
     * findOne
     *
     * @param \Product $product
     * @return \library\Product\Image|null
     */
    public function findOne(\Product $product)
    {
        $this->dbo->setQuery(sprintf('select * from `%s`  where `product_id` = %d order by id asc limit 1', $this->tableName, $product->getId()));
        if (null === $row = $this->dbo->fetchObjectRow()) {
            return null;
        }
        return new Image($row);
    }

    /**
     * find cover image
     *
     * @param \Product $product
     * @return \library\Product\Image|null
     */
    public function findCover(\Product $product)
    {
        $this->dbo->setQuery(sprintf('select * from `%s`  where `product_id` = %d AND `cover` = 1 limit 1', $this->tableName, $product->getId()));
        if (null === $row = $this->dbo->fetchObjectRow()) {
            return null;
        }
        return new Image($row);
    }

}
