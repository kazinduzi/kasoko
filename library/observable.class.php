<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Description of Observable
 *
 * @author Emmanuel_Leonie
 */
abstract class Observable {     
    /**
    * Array of Observers
    * @access private
    * @var array
    */
    private $observers = array();
    
    public function __construct() 
    {
        $this->observers = array();
    }
    
    /**
    * Calls the update() function using the reference to each
    * registered observer, passing an optional argument for the
    * event - used by children of Observable
    * @return void
    */
    public function notifyAll($arg = null)
    {         
        foreach (array_keys($this->observers) as $key) 
        {
              $this->observers[$key]->update(&$this, $arg);
        }
    }
    
    /**
    * Attaches an observer to the observable
    * @return void
    */
    public function addObserver($observer)
    {
        $this->observers[] = $observer;
    }
}
