<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author Emmanuel_Leonie
 */
final class Config implements ArrayAccess
{

    /**
     * @staticvar For multiple instances of Config class
     */
    protected static $instances = array();

    /**
     * @staticvar For one configuration file to be loaded
     */
    protected static $instance = array();

    /**
     * @var $paths folders where the configs are stored
     */
    protected $paths = array(APP_PATH, KAZINDUZI_PATH);

    /**
     * @var $config variable to hold the configuration data
     */
    private $config;

    public function __construct($group = null)
    {
        if ($group === null) {
            $group = 'main';
        }
        // Try loading config data according to the provided configuration group
        $this->load($group);
    }

    public function load($filename)
    {
        // Find the file in the configs folder include it, then fetch the encapsulated data within it
        foreach ($this->paths as $path) {
            if (file_exists($file = $path . DS . 'configs' . DS . $filename . EXT)) {
                require $file;
                $this->config = & $config;
                unset($config);
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------
     * Loading only one configuration file
     * -----------------------------------------------------------------------------------------------------
     * @uses The param to find the config file and load it to be accessed
     * @param This param can be ('main'|'session'|'database') or any
     * @return Config Object for the specified param.
     */
    public static function instance()
    {
        $groups = func_get_args();
        if ($groups == array())
            $groups = array('main');
        foreach ($groups as $key => $grp) {
            if (empty(self::$instance[$grp])) {
                // We create a new instance of Config
                self::$instance[$grp] = new self($grp);
            }
        }
        return self::$instance[$grp];
    }

    /**
     * -----------------------------------------------------------------------------------------------------
     * Loading multiple configs files at once
     * -----------------------------------------------------------------------------------------------------
     *
     * Try to load multiple configuration files at once in an Array
     * @access the appropriated data using the appropriate filename without extension as key
     * @example:
     * $configs = Config::instances(array('foo','bar'))
     * foreach($configs as $key => $config)
     * {
     *     $configs[$key] = $config;
     * }
     * $configs is the array of Config Objects for foo and bar config files
     */
    public static function instances()
    {
        $groups = func_get_args();
        if ($groups == array())
            $groups = array('main');
        foreach ($groups as $key => $grp) {
            if (is_array($grp) AND ! empty($grp)) {
                foreach ($grp as $key => $grp) {
                    if (empty(self::$instances[$grp])) {
                        // We create a new instance of Config
                        self::$instances[$grp] = new self($grp);
                    }
                }
            }
            //
            if (empty(self::$instances[$grp])) {
                // We create a new instance of Config
                self::$instances[$grp] = new self($grp);
            }
        }
        return self::$instances;
    }

    /**
     * Return the current group in serialized form.
     *
     *     echo $config;
     *
     * @return  string
     */
    public function __toString()
    {
        return serialize($this->config);
    }

    public function toArray()
    {
        return $this->as_array();
    }

    public function as_array()
    {
        return (array) $this->config;
    }

    /**
     * Get a variable from the configuration or return the default value.
     *
     *     $value = $config->get($key);
     *
     * @param   string   array key
     * @param   mixed    default value
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : $default;
    }

    /*     * *
     *
     */

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /*     * *
     *
     */

    public function offsetGet($offset)
    {
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }

    /**
     * Sets a value in the configuration array.
     *
     *     $config->set($key, $new_value);
     *
     * @param   string   array key
     * @param   mixed    array value
     * @return  $this
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /*     * *
     *
     */

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->config[] = $value;
        } else {
            $this->config[$offset] = $value;
        }
    }

    /*     * *
     *
     */

    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

}
