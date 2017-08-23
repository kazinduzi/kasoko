<?php

namespace models\Manufacturer;

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Manufacturer extends \Model
{

    const MANUFACTURER_TABLE = 'manufacturer';
    const MANUFACTURER_PRIMARY_KEY = 'manufacturer_id';

    public $table = self::MANUFACTURER_TABLE;

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
    protected $pk = self::MANUFACTURER_PRIMARY_KEY;
    protected $id;

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

}
