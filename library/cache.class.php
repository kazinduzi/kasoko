<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Description of cache
 *
 * @author Emmanuel_Leonie
 */
abstract class Cache
{

    /**
     *
     */
    const TTL = 3600;

    /**
     * @var type
     */
    private static $instance;

    /**
     * @param string $group
     * @return Cache object
     */
    public static function getInstance()
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }
        $config = Kazinduzi::getConfig('cache')->toArray();
        $cacheClassName = 'Cache' . ucfirst($config['driver']);
        self::$instance = new $cacheClassName($config);
        return self::$instance;
    }

    /**
     * Retrieve a cached value entry by id.
     *
     * @param   string   id of cache to entry
     * @param   string   default value to return if cache miss
     * @return  mixed
     * @throws  Exception
     */
    abstract public function get($id);

    /**
     * Set a value to cache with id and lifetime
     *
     * @param   string   id of cache entry
     * @param   string   data to set to cache
     * @param   integer  lifetime in seconds
     * @return  boolean
     */
    abstract public function set($id, $data, $lifetime = 3600);

    /**
     * Delete a cache entry based on id
     *
     * @param   string   id to remove from cache
     * @return  boolean
     */
    abstract public function delete($id);

    /**
     * Delete all cache entries.
     *
     * Beware of using this method when
     * using shared memory cache systems, as it will wipe every
     * entry within the system for all clients.
     *
     * @return  boolean
     */
    abstract public function deleteAll();

    /**
     * Replaces troublesome characters with underscores.
     *
     * @param   string   id of cache to sanitize
     * @return  string
     */
    protected function _sanitize_id($id)
    {
        // Change slashes and spaces to underscores
        return str_replace(array('/', '\\', ' '), '_', $id);
    }

    /**
     * Overload the __clone() method to prevent cloning
     * @return  void
     * @throws  Cache_Exception
     */
    private function __clone()
    {
        throw new Exception('Cloning of this object is forbidden');
    }

}
