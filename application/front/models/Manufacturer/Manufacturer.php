<?php

namespace models\Manufacturer;

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Manufacturer extends \Model
{ 
    const SORT_ALPHA = 'alpha';
    const SORT_ALPHA_REV = 'alpha_reverse';
    const SORT_PRICE_MIN = 'price_min';
    const SORT_PRICE_MAX = 'price_max';
    
    const MANUFACTURER_TABLE = 'manufacturer';
    const MANUFACTURER_PRIMARY_KEY = 'manufacturer_id';

    public $table = self::MANUFACTURER_TABLE;
    protected $pk = self::MANUFACTURER_PRIMARY_KEY;
    protected $id;

    /**
     * Place for relations of our models
     * {$hasMany} | {$hasOne} | {$belongTo} | {$hasMany_through}
     * @var array
     */
    public $hasMany = array(
        'products' => array(
            'model' => 'product',
            'through' => 'product_manufacturer',
            'foreign_key' => 'manufacturer_id',
            'far_key' => 'product_id'
        )
    );

    /**
     * 
     * @param boolean $active
     * @return \models\Manufacturer\Manufacturer
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function isActive()
    {
        return (bool) $this->active === true;
    }

    /**
     * 
     * @return array
     */
    public function getProducts(array $opts = array())
    {
        $opts['order'] = !empty($opts['order']) ? $opts['order'] : Manufacturer::SORT_ALPHA;
	$products = $this->products ?: array();        
	uasort($products, function ($x, $y) use($opts) {	    
	    switch ($opts['order']){
		default :
		case Manufacturer::SORT_ALPHA:
		    return strcasecmp($x->name, $y->name);			
		case Manufacturer::SORT_ALPHA_REV:
		    return strcasecmp($y->name, $x->name);			
		case Manufacturer::SORT_PRICE_MIN:
		    return ($x->price - $y->price);
		case Manufacturer::SORT_PRICE_MAX:
		    return ($y->price - $x->price);		    
	    }	       
	});	
	return new \ArrayIterator($products);
    }

    /**
     * 
     * @return array
     */
    public function getAllActive()
    {
        return $this->findAll('active=1');
    }

    /**
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->findAll();
    }

    /**
     * 
     * @param string $slug
     * @return type
     */
    public function getBySlug($slug)
    {
        $whereClause = sprintf('slug=\'%s\'', $this->getDbo()->real_escape_string($slug));
        return $this->findByAttr('*', $whereClause);
    }
    

}
