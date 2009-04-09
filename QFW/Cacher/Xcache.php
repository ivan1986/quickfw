<?php

/**
 * Xcache variable cache interface
 *
 * @package RELAX
 * @author Petrenko Andrey
 * @version 0.1
 * @copyright RosBusinessConsulting
 */

class Cacher_Xcache implements Zend_Cache_Backend_Interface
{
	public function setDirectives($directives)
	{
	}

	/**
	 * Set (add) value on server
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl Time after which server will delete value
	 * @return bool
	 */
	public function save($data, $id, $tags = array(), $specificLifetime = 3600)
	{
		return xcache_set($id, $data, $specificLifetime);
	}

	/**
	 * Get value from server
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function load($id, $doNotTest = false)
	{
		return xcache_get($id);
	}

	/**
	 * Check if variable with this key is set on server
	 *
	 * @param string $key
	 * @return bool
	 */
	public function test($id)
	{
		return xcache_isset($id);
	}

	/**
	 * Deletes variable from server
	 *
	 * @param string $key
	 * @return bool
	 */
	public function remove($id)
	{
		return xcache_unset($id);
	}

	public function clean($mode = CACHE_CLR_ALL, $tags = array())
	{

	}

}

?>