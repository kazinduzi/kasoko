<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

Class Registry
{

    /**
     *
     * @var type
     */
    private static $instance = null;
    /**
     * @the vars array
     * @access private
     */
    private $vars = array();

    /**
     *
     * @return type
     */
    private function  __construct()
    {
    }

    /**
     *
     * @return type
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /**
     * @get variables
     * @param mixed $index
     * @return mixed
     */
    public function __get($index)
    {
        if (array_key_exists($index, $this->vars))
            return $this->vars[$index];
        return null;
    }

    /**
     * @set undefined vars
     * @param string $index
     * @param mixed $value
     * @return void
     */
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function  __isset($name)
    {
        // you could also use isset() here
        return array_key_exists($name, $this->vars);
    }

    /**
     *
     * @param type $name
     */
    public function  __unset($name)
    {
        // you could also use unset() here
        if (array_key_exists($name, $this->vars))
            unset($this->vars[$name]);
    }

    /**
     * method to stringfy the Registry class
     * @access public
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     *
     * @return type
     */
    public function __toString()
    {
        return (array)$this->vars;
    }

    /**
     *
     * @return type
     */
    private function  __clone()
    {
    }

}
