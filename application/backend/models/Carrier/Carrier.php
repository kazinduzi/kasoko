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
     * @return boolean
     */
    public function isActive()
    {
        return $this->active == true;
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