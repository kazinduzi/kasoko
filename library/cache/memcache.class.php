<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Description of memcache
 *
 * @author Emmanuel_Leonie
 */
class CacheMemcache extends Cache
{

    /**
     *  Memcache has a maximum cache lifetime of 30 days
     */
    const CACHE_CEILING = 2592000;

    /**
     * Memcache resource
     * @var Memcache
     */
    protected $_memcache;

    /**
     * Flags to use when storing values
     * @var string
     */
    protected $_flag;

    /**
     * The configuration for memcache config data
     * @var array
     */
    protected $_config = array();


    /**
     * Constructor of the Memcache Class
     *
     * @param array $config
     * @throws Exception
     */
    protected function __construct(array $config)
    {
        $this->_config = $config;
        // Check for the memcache extention
        if (!extension_loaded('memcache')) {
            throw new Exception('Memcache PHP extention not loaded');
        }
        // Setup Memcache
        $this->_memcache = new Memcache();
        foreach ($this->_config['servers'] as $server) {
            if (!$server) {
                throw new Exception('No Memcache servers defined in configuration');
            }
            if ($this->_config['compatibility']) {
                // No status for compatibility mode (#ZF-5887)
                $this->_memcache->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retry_interval']);
            } else {
                $this->_memcache->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retry_interval'], $server['status'], $server['failure_callback']);
            }

        }
        if ($this->_config['compression']) {
            $this->_flag = MEMCACHE_COMPRESSED;
        } else {
            $this->_flag = 0;
        }
    }

    /**
     * Test if a cache is available or not (for the given id)
     * @param  string $id Cache id
     * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $tmp = $this->_memcache->get($id);
        if (is_array($tmp)) {
            return $tmp[1];
        }
        return false;
    }

    /**
     *
     * @param type $id
     * @param type $default
     * @return type
     */
    public function get($id)
    {
        return $this->load($id);
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string $id Cache id
     * @return string|false cached datas
     */
    public function load($id)
    {
        $tmp = $this->_memcache->get($this->_sanitize_id($id));
        if (is_array($tmp) && isset($tmp[0])) {
            return $tmp[0];
        }
        return false;
    }

    /**
     *
     * @param type $id
     * @param type $data
     * @param type $ttl
     * @return type
     */
    public function set($id, $data, $ttl = 3600)
    {
        if ($ttl > self::CACHE_CEILING) {
            $ttl = self::CACHE_CEILING + time(); // Set the lifetime to maximum cache time
        } elseif ($ttl > 0) {
            $ttl += time();
        } else {
            $ttl = 0; // Normalise the lifetime
        }
        try {
            $bool = @$this->_memcache->set($this->_sanitize_id($id), array($data, time(), $ttl), $this->_flag, $ttl);
        } catch (\Exception $e) {
            print_r($e);
        }
        return $bool;
    }

    /**
     * Stores variable var with key only if such key doesn't exist at the server yet.
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function add($id, $data, $ttl = 60)
    {
        return $this->_memcached->add($this->_sanitize_id($id), array($data, time(), $ttl), $ttl);
    }

    /**
     *
     *
     * @param string $id
     * @param int $timeout
     * @return bool
     */
    public function delete($id, $timeout = 0)
    {
        return $this->_memcache->delete($this->_sanitize_id($id), $timeout);
    }

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id, $timeout = 0)
    {
        return $this->_memcache->delete($id, $timeout);
    }

    /**
     *
     * @return type
     */
    public function deleteAll()
    {
        $result = $this->_memcache->flush();
        // We must sleep after flushing, or overwriting will not work!
        // @see http://php.net/manual/en/function.memcache-flush.php#81420
        $time = time() + 1; //one second future
        while (time() < $time) ;
        return $result;
    }

    /**
     * Clean some cache records
     *
     * @return bool
     */
    public function clean()
    {
        $result = $this->_memcache->flush();
        $time = time() + 1; //one second future
        while (time() < $time) ;
        return $result;
    }

    /**
     * Cache Info
     * @return    mixed    array on success, false on failure
     */
    public function info()
    {
        return $this->_memcache->getStats();
    }

    /**
     * Get Cache Metadata
     * @param    mixed    key to get cache metadata on
     * @return    mixed    false on failure, array on success.
     */
    public function metadata($id)
    {
        $stored = $this->_memcache->get($id);
        if (count($stored) !== 3) {
            return false;
        }
        list($data, $time, $ttl) = $stored;
        return array(
            'expire' => $time + $ttl,
            'mtime' => $time,
            'data' => $data
        );
    }

    /**
     *
     * @param type $id
     * @param type $step
     * @return type
     */
    public function increment($id, $step = 1)
    {
        return $this->_memcache->increment($id, $step);
    }

    /**
     *
     * @param type $id
     * @param type $step
     * @return type
     */
    public function decrement($id, $step = 1)
    {
        return $this->_memcache->decrement($id, $step);
    }

}