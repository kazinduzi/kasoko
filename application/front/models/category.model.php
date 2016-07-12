<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Category extends Model
{
    const LIMIT_DEFAULT = 10;
    const CATEGORY_TABLE = 'category';
    const PRODUCT_TABLE = 'product';
    const CATEGORY_PRODUCT_TABLE = 'category_product';
    //
    const SORT_ALPHA = 'alpha';
    const SORT_ALPHA_REV = 'alpha_reverse';
    const SORT_PRICE_MIN = 'price_min';
    const SORT_PRICE_MAX = 'price_max';

    /**
     * Table name of the category
     * @var string
     */
    public $table = self::CATEGORY_TABLE;

    /**
     * Place for relations of our models
     * {$hasMany} | {$hasOne} | {$yelongTo} | {$hasMany_through}
     * @var array
     */
    public $hasMany = array(
        'product' => array(
            'model' => 'product',
            'through' => 'category_product',
            'foreign_key' => 'category_id',
            'far_key' => 'product_id'
        )
    );
    /**
     * The primary key category table
     * @var string
     */
    protected $pk = 'category_id';

    /**
     * The category id for this model
     * @var integer
     */
    protected $id;

    /**
     *
     * @param mixed $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * Static get the category model by SEO name
     * @param string $seo_name
     * @return \static
     * @throws Exception
     */
    public static function getByName($seo_name)
    {
        self::$db = Kazinduzi::db();
        self::$db->execute("SELECT * FROM `" . self::CATEGORY_TABLE . "` WHERE `seo_name` = '" . static::$db->real_escape_string($seo_name) . "' LIMIT 1");
        $values = self::$db->fetchAssocRow();
        if (!$values) {
            throw new \Exception('Empty data');
        }
        return new static($values);
    }

    /**
     * Is category top
     *
     * @return boolean
     */
    public function isTop()
    {
        return (int)$this->parent_id === 0;
    }

    /**
     * Is category a child
     *
     * @return boolean
     */
    public function isChild()
    {
        return (int)$this->parent_id !== 0;
    }

    /**
     * Check  if category is live
     *
     * @return boolean
     */
    public function isLive()
    {
        return (bool)$this->status === true;
    }

    /**
     * Check  if category is visible
     *
     * @return boolean
     */
    public function visibleInMenu()
    {
        return (bool)($this->in_menu || is_null($this->in_menu));
    }

    /**
     * Does the category have children?
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return count($this->getChildren()) > 0;
    }

    /**
     * Get category's children
     *
     * @return array
     */
    public function getChildren()
    {
        $children = array();
        $this->getDbo()->setQuery('select * from `category` where `parent_id` = ' . $this->getId());
        if (null !== $rows = $this->getDbo()->fetchAssocList()) {
            foreach ($rows as $row) {
                $children[] = new static($row);
            }
        }
        return new ArrayIterator($children);
    }

    /**
     * Does the category have active children?
     *
     * @return boolean
     */
    public function hasActiveChildren()
    {
        return count($this->getActiveChildren()) > 0;
    }

    /**
     * Get category's active children
     * @return \ArrayIterator
     */
    public function getActiveChildren()
    {
        $children = array();
        $this->getDbo()->setQuery('select * from `category` where `status` = 1 AND `parent_id` = ' . $this->getId());
        if (null !== $rows = $this->getDbo()->fetchAssocList()) {
            foreach ($rows as $row) {
                $children[] = new static($row);
            }
        }
        return new ArrayIterator($children);
    }

    /**
     *
     * @param array $data
     * @return type
     * @throws Exception
     */
    public function addCategory(array $data)
    {
        if (!$data) {
            throw new \Exception('Invalid data for model provided at line:' . __LINE__);
        }
        $this->values = $data;
        return $this->saveRecord();
    }

    /**
     *
     * @param array $data
     * @return type
     * @throws Exception
     */
    public function editCategory(array $data)
    {
        if (!$data[$this->pk]) {
            throw new \Exception('Invalid category id is provided at line:' . __LINE__);
        }
        $this->values = $data;
        return $this->saveRecord();
    }

    /**
     *
     * @return boolean
     */
    public function deleteCategory()
    {
        $this->deleteRecord();
    }

    /**
     *
     * @return mixed
     */
    public function getAll()
    {
        return new ArrayIterator($this->findAll());
    }

    /**
     * Get all active categories
     *
     * @return array
     */
    public function getAllActive()
    {
        return new ArrayIterator($this->findAll('`status`=1'));
    }

    /**
     *
     * @return mixed
     */
    public function getProducts(array $opts = array())
    {
        $opts['order'] = !empty($opts['order']) ? $opts['order'] : self::SORT_ALPHA;
        $products = $this->product ?: array();
        uasort($products, function ($x, $y) use ($opts) {
            switch ($opts['order']) {
                default :
                case \Category::SORT_ALPHA:
                    return strcasecmp($x->name, $y->name);
                case \Category::SORT_ALPHA_REV:
                    return strcasecmp($y->name, $x->name);
                case \Category::SORT_PRICE_MIN:
                    return ($x->price - $y->price);
                case \Category::SORT_PRICE_MAX:
                    return ($y->price - $x->price);
            }
        });
        return new \ArrayIterator($products);
    }

}
