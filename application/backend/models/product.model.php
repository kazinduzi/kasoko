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
use library\Product\ImageProvider as ProductImageProvider;
use models\Manufacturer\Manufacturer;
use models\Product\Attributes;

/**
 * Product model class
 */
class Product extends Model
{

    const LIMIT_DEFAULT = 5;
    const MANUFACTURER_PRODUCT_TABLE = 'product_manufacturer';
    const CATEGORY_PRODUCT_TABLE = 'category_product';

    /**
     * Static get the category model by SKU name
     *
     * @param string $sku
     * @return \static
     * @throws Exception
     */
    public static function getBySku($sku)
    {
        self::$db = Kazinduzi::db();
        $table = strtolower(get_class());
        self::$db->execute(sprintf("SELECT * FROM `%s` WHERE `sku` = '%s' LIMIT 1", $table, $sku));
        $values = self::$db->fetchAssocRow();
        if (!$values) {
            throw new Exception('Empty data');
        }
        return new static($values);
    }

    /**
     * Get stock statuses
     *
     * @return array
     */
    public static function getStockStatuses()
    {
        $db = Kazinduzi::db();
        $db->query('SELECT * FROM `stock_status`');
        return $db->fetchObjectList();
    }

    /**
     * Table name of the product
     *
     * @var string
     */
    public $table = 'product';

    /**
     * The primary key product table
     *
     * @var string
     */
    protected $pk = 'product_id';

    /**
     * The product id for this model
     *
     * @var integer
     */
    protected $id;

    /**
     * Place for relations of our models
     *
     * {$hasMany} | {$hasOne} | {$belongTo} | {$hasMany_through}
     * @var array
     */
    public $hasMany = array(
        'category' => array(
            'model' => 'category',
            'through' => 'category_product',
            'foreign_key' => 'product_id',
            'far_key' => 'category_id'
        ),

        /**
         * Simple HAS-MANY, without join through table, @see \Model::_get()
         */
        'attributeValues' => array(
            'model' => '\\models\\Product\\AttributeValue',
            //'through' => 'product_attributes',
            'foreign_key' => 'product_id',
            //'far_key' => 'attribute_id'
        ),

        'product_attribute_configurations' => [
            'model' => '\\models\\Product\\ProductAttributeConfiguration',
            'foreign_key' => 'product_id',
        ],
    );

    public $belongsTo = array(
        'manufacturer' => array(
            'model' => '\\models\\Manufacturer\\Manufacturer',
            'foreign_key' => 'product_id',
            'through' => 'product_manufacturer',
            'far_key' => 'manufacturer_id'
        )
    );

    /**
     * @var integer
     */
    protected $manufacturer_id;

    /**
     * @var array
     */
    protected $categories = array();

    /**
     * @var ProductImageProvider
     */
    protected $productImageProvider;

    /**
     * @param array $categories
     * @return $this
     */
    public function setCategories(array $categories = array())
    {
        $this->categories = $categories;
        return $this;
    }

    public function setManufacturerId($manufacturer)
    {
        $this->manufacturer_id = $manufacturer;
        return $this;
    }

    public function getManufacturerId()
    {
        return $this->manufacturer_id;
    }

    /**
     * Save relations
     *
     * @staticvar type $Dbo
     * @return boolean
     */
    protected function saveRelations()
    {
        if (!empty($this->categories)) {
            $this->getDbo()->autocommit(false);
            try {
                foreach ($this->categories as $categoryId) {
                    $this->getDbo()->setQuery(sprintf("INSERT INTO `category_product` SET `category_id` = %d, `product_id` = %d;", (int)$categoryId, $this->getId()));
                    $this->getDbo()->execute();
                }
                $this->getDbo()->commit();
                return true;
            } catch (\Exception $e) {
                $this->getDbo()->rollback();
                print_r($e);
            }
        }
    }

    protected function saveManufacturer()
    {
        $this->getDbo()->autocommit(false);
        try {
            $this->getDbo()->setQuery(sprintf("replace into `%s` set `product_id` = %d, `manufacturer_id` = %d", self::MANUFACTURER_PRODUCT_TABLE, $this->getId(), $this->getManufacturerId()));
            $this->getDbo()->execute();
            $this->getDbo()->commit();
        } catch (Exception $ex) {
            $this->getDbo()->rollback();
            print_r($ex);
            die;
        }
    }

    /**
     * Reset the relations
     */
    protected function resetRelations()
    {
        $this->getDbo()->autocommit(false);
        try {
            $this->getDbo()->setQuery(sprintf("DELETE FROM `%s` WHERE `product_id` = %d", self::CATEGORY_PRODUCT_TABLE, $this->getId()));
            $this->getDbo()->execute();
            $this->getDbo()->commit();
        } catch (\Exception $e) {
            $this->getDbo()->rollback();
            print_r($e);
        }
    }

    /**
     * Reset the product combinations|attributes
     */
    public function resetProductAttributes()
    {
        $this->getDbo()->autocommit(false);
        try {
            $this->getDbo()->setQuery(sprintf("DELETE FROM `product_attributes` WHERE `product_id` = %d", $this->getId()));
            $this->getDbo()->execute();
            $this->getDbo()->commit();
        } catch (\Exception $e) {
            $this->getDbo()->rollback();
            print_r($e);
        }
    }


    /**
     * @return $this
     */
    public function save()
    {
        parent::save();
        $this->resetRelations();
        if (!empty($this->categories)) {
            $this->saveRelations();
        }
        if (!empty($this->manufacturer_id)) {
            $this->saveManufacturer();
        }
        return $this;
    }

    /**
     * Save the product attributes
     *
     * @param array $data
     * @param array $impactPrice
     * @param array $impactQuantity
     * @internal param Product $product
     */
    public function saveProductAttributes(array $data, array $impactPrice, array $impactQuantity)
    {
        if (is_array($impactPrice)) {
            $impactPrice = array_filter($impactPrice);
        }

        if (is_array($impactQuantity)) {
            $impactQuantity = array_filter($impactQuantity);
        }

        $this->resetProductAttributes();
        if (!empty($data)) {
            foreach ($data as $attrId) {
                $attrValue = new \models\Product\AttributeValue();
                $attrValue->product_id = $this->getId();
                $attrValue->price_impact = isset($impactPrice[$attrId]) ? (float)$impactPrice[$attrId] :  0.00;
                $attrValue->quantity_impact = isset($impactQuantity[$attrId]) ? (float)$impactQuantity[$attrId] :  0.00;
                $attrValue->save();
            }
        }
    }

    /**
     *
     * @return type
     */
    public static function getAll()
    {
        return static::getInstance()->findAll();
    }

    /**
     *
     * @param integer $limit
     * @return array
     */
    public static function getLatest($limit = self::LIMIT_DEFAULT)
    {
        return self::model()->findBySql(sprintf("SELECT * FROM `product` ORDER BY `product_id` DESC LIMIT 0, %d;", (int)$limit));
    }

    /**
     *
     * @return array
     */
    public static function getSpecials()
    {
        return self::model()->findBySql("SELECT * FROM `product_special` JOIN `product` USING(`product_id`)");
    }

    /**
     *
     * @param string $sku
     * @return type
     */
    public static function getSpecialBySlug($sku)
    {
        return self::model()->findBySql(sprintf("SELECT `p`.*, `ps`.`price` AS `special_price`, `ps`.* FROM `product` AS `p` INNER JOIN `product_special` AS `ps` USING (`product_id`) WHERE `p`.`sku` = '%s' LIMIT 1;", $sku));
    }

    /**
     *
     * @return array
     */
    public function getProductImages()
    {
        if (null === $this->productImageProvider) {
            $this->productImageProvider = new ProductImageProvider($this);
        }
        return $this->productImageProvider->findByProduct($this);
    }

    /**
     *
     * @return type
     */
    public function getFirstProductImage()
    {
        if (null === $this->productImageProvider) {
            $this->productImageProvider = new ProductImageProvider($this);
        }
        return $this->productImageProvider->findOne($this);
    }

    /**
     *
     * @return type
     */
    public function getCoverProductImage()
    {
        if (null === $this->productImageProvider) {
            $this->productImageProvider = new ProductImageProvider($this);
        }
        return $this->productImageProvider->findCover($this);
    }

    public function getThumbImage()
    {
        if ($this->getCoverProductImage()) {
            return $this->getCoverProductImage()->getThumb();
        } elseif ($this->getFirstProductImage()) {
            return $this->getFirstProductImage()->getThumb();
        }
    }

    /**
     *
     * @return type
     */
    public function getManufacturer()
    {
        $this->getDbo()->query(sprintf("SELECT `m`.* FROM `product_manufacturer` as `pm` JOIN `manufacturer` as `m` USING (`manufacturer_id`) WHERE `pm`.`product_id` = %d", $this->getId()));
        if (null === $row = $this->getDbo()->fetchAssocRow()) {
            return null;
        }
        return new Manufacturer($row);
    }

    /**
     *
     * @return type
     */
    public function getStockStatus()
    {
        $this->getDbo()->query(sprintf("SELECT `ss`.* FROM `stock_status` AS `ss` JOIN `product` AS `p` USING (`stock_status_id`) WHERE `p`.`product_id` = %d", $this->getId()));
        return $this->getDbo()->fetchObjectRow();
    }

    /**
     *
     * @return \Product
     */
    public function getRelated()
    {
        $this->getDbo()->query(sprintf("SELECT `pr`.`related_id` FROM `product_related` AS `pr` WHERE `pr`.`product_id` = %d", $this->getId()));
        $related = null;
        foreach ($this->getDbo()->fetchAssocList() as $relatedId) {
            $related[] = new static((int)$relatedId);
        }
        return $related;
    }

    /**
     *
     * @return type
     */
    public function getCategories()
    {
        return $this->category;
    }

    /**
     * @return bool
     */
    public function hasProductAttributes()
    {
        return count($this->attributeValues) > 0;
    }

    /**
     * @return array
     */
    public function getProductAttributes()
    {
        return $this->attributeValues;

    }

    /**
     * @return array|type
     */
    public function getProductAttributeConfigurations()
    {
        $productAttributeConfigurations = [];
        if (count($this->getProductAttributes())) {
            foreach($this->getProductAttributes() as $productAttribute) {
                $productAttributeConfigurations[] = $productAttribute->getProductAttributeConfigurations();
            }
        }
        return $productAttributeConfigurations;
    }

}
