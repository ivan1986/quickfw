<?php

/**
 * Apc variable cache interface
 */

class Cacher_Apc implements Zend_Cache_Backend_Interface
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
		return apc_store($id, $data);
	}

	/**
	 * Get value from server
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function load($id, $doNotTest = false)
	{
		return apc_fetch($id);
	}

	/**
	 * Check if variable with this key is set on server
	 *
	 * @param string $key
	 * @return bool
	 */
	public function test($id)
	{
		return apc_exists($id);
	}

	/**
	 * Deletes variable from server
	 *
	 * @param string $key
	 * @return bool
	 */
	public function remove($id)
	{
		return apc_delete($id);
	}

	public function clean($mode = CACHE_CLR_ALL, $tags = array())
	{
		apc_clear_cache('user');
	}

}
