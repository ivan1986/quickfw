<?php

/**
 * Memcached server interface
 * 
 * @package RELAX
 * @author Petrenko Andrey
 * @version 0.1
 * @copyright RosBusinessConsulting
 */
class Cacher_Memcache
{
	protected static $connection = NULL;
	
    public function __construct($host='localhost', $port=11211)
    {
        $this->addServer($host, $port);
    }
    
    /**
     * Connect to memcached server or add server in pool
     *
     * @param string $host
     * @param int $port
     */
    public function addServer($host='localhost', $port=11211)
    {
        if (self::$connection === NULL)
            self::$connection = memcache_pconnect($host, $port);
        else 
            memcache_add_server(self::$connection, $host, $port);
    }
    
    /**
     * Set (add) value on server
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl Time after which server will delete value 
     */
    public function set($key, $value)
    {
        if (self::$connection === NULL) return ;
        memcache_set(self::$connection, $key, $value);
    }
    
    /**
     * Get value from server
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (self::$connection === NULL) return false;
        return memcache_get(self::$connection, $key);
    }
    
    /**
     * Check if variable with this key is set on server
     *
     * @param string $key
     * @return bool
     */
    public function incache($key)
    {
        return ($this->get($key)!==false);
    }
    
    /**
     * Deletes variable from server
     *
     * @param string $key
     * @param int $timeout Timeout after which it will be deleted
     */
    public function delete($key, $timeout = 0)
    {
        if (self::$connection === NULL) return ;
        memcache_delete(self::$connection, $key, $timeout);
    }

}
?>