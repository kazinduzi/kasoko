<?php
namespace models\Carrier;

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Carrier extends \Model 
{
    
    protected $table = 'carrier';
    
    /**
     * 
     * @param boolean $active
     * @return \models\Manufacturer\Manufacturer
     */
    public function setActive($active)
    {
        $this->active = (bool)$active;
        return $this;
    }
    
    /**
     * 
     * @param boolean $free
     * @return \models\Carrier\Carrier
     */
    public function setFree($free)
    {
        $this->is_free = (bool)$free;
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isActive()
    {
        return (bool)$this->active === true;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isFree()
    {
        return (bool)$this->is_free === true;
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