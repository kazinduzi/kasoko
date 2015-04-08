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
use library\Currency;

/**
 * Product model class
 */
class Product extends Model
{

    const LIMIT_DEFAULT = 10;
    const PRODUCT_TABLE = 'product';
    const MANUFACTURER_PRODUCT_TABLE = 'product_manufacturer';
    const CATEGORY_PRODUCT_TABLE = 'category_product';
    const SPECIAL_PRODUCT_TABLE = 'product_special';

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
    public static function getLatest(array $opts = array())
    {
	$order_qry = '';
	$limit = empty($opts['limit']) ? self::LIMIT_DEFAULT : $opts['limit'];
	if (empty($opts['order'])) {
	    $order_qry .= 'ORDER BY `product_id` DESC';
	} else {
	    switch ($opts['order']){
		case 'alpha':
		    $order_qry .= 'order by `name` ASC';
		    break;
		case 'alpha_reverse':
		    $order_qry .= 'ORDER BY `name` DESC';
		    break;
		case 'price_min':
		    $order_qry .= 'ORDER BY `price` ASC';
		    break;
		case 'price_max':
		    $order_qry .= 'ORDER BY `price` DESC';
		    break;
		default :
		    $order_qry .= 'ORDER BY `product_id` DESC';
		    break;
	    }
	}		
	return static::model()->findBySql(sprintf("SELECT * FROM `product` %s LIMIT 0, %d;", $order_qry, (int)$limit));
    }

    /**
     *
     * @return array
     */
    public static function getSpecials()
    {
	return static::model()->findBySql("SELECT * FROM `product_special` JOIN `product` USING(`product_id`)");
    }

    /**
     *
     * @param string $slug
     * @return type
     */
    public static function getSpecialBySlug($slug)
    {
	return static::model()->findBySql(sprintf("SELECT `p`.*, "
				. "(SELECT `ps`.`price` FROM `product_special` AS `ps` WHERE `ps`.`product_id` = `p`.`product_id`) as special_price "
				. "FROM `product` AS `p` WHERE `p`.`slug` = '%s' LIMIT 1;", self::$db->real_escape_string($slug)));
    }

    /**
     * @param Product $product
     * @return type
     */
    public static function getSpecial(Product $product)
    {
	return static::model()->findBySql(sprintf("SELECT `p`.*, "
				. "(SELECT `ps`.`price` FROM `product_special` AS `ps` WHERE `ps`.`product_id` = `p`.`product_id`) as special_price "
				. "FROM `product` AS `p` WHERE `p`.`product_id` = '%d';", $product->getId()));
    }

    /**
     * Table name of the product
     * 
     * @var string
     */
    public $table = self::PRODUCT_TABLE;

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
		    $this->getDbo()->setQuery(sprintf("INSERT INTO `category_product` SET `category_id` = %d, `product_id` = %d;", (int) $categoryId, $this->getId()));
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
     * Save
     * 
     * @return \news\ModelArticle
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
     * 
     * @return type
     */
    public function getCategories()
    {
	return $this->category;
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
	    $related[] = new static((int) $relatedId);
	}
	return $related;
    }

    /**
     * Update the viewed
     * 
     * @return mixed
     * @throws Exception
     */
    public function updateViewed()
    {
	$this->getDbo()->autocommit(false);
	try {
	    $this->getDbo()->setQuery(sprintf("UPDATE `%s` SET viewed = (viewed + 1) WHERE product_id = %d;", $this->table, $this->getId()));
	    $this->getDbo()->execute();
	    $this->getDbo()->commit();
	    return $this->getDbo()->affected_rows();
	} catch (\Exception $e) {
	    $this->getDbo()->rollback();
	    throw $e;
	}
    }

}
