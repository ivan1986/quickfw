<?php

/**
* Xcache variable cache interface
 * 
 * @package RELAX
 * @author Petrenko Andrey
 * @version 0.1
 * @copyright RosBusinessConsulting
 */
class Cacher_Xcache
{
    /**
     * Set (add) value on server
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl Time after which server will delete value 
     * @return bool
     */
    public function set($key, $value, $ttl=0)
    {
        return xcache_set($key, $value, $ttl);
    }
    
    /**
     * Get value from server
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return xcache_get($key);
    }
    
    /**
     * Check if variable with this key is set on server
     *
     * @param string $key
     * @return bool
     */
    public function incache($key)
    {
        return xcache_isset($key);        
    }
    
    /**
     * Deletes variable from server
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return xcache_unset($key);
    }
}

?>