<?php

/**
 * Memcached server interface
 *
 * @package RELAX
 * @author Petrenko Andrey
 * @version 0.1
 * @copyright RosBusinessConsulting
 */

require_once(QFWPATH.'/QuickFW/Cacher/Interface.php');

class Cacher_Memcache implements Zend_Cache_Backend_Interface
{
	protected static $connection = NULL;

	public function __construct()
	{
		//$this->addServer($host, $port);
	}

	public function setDirectives($directives)
	{
		$host = isset($directives['host'])?$directives['host']:'localhost';
		$port = isset($directives['port'])?$directives['port']:'11211';
		$this->addServer($host,$port);
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
	public function save($data, $id, $tags = array(), $specificLifetime = 3600)
	{
		if (self::$connection === NULL) return ;
		memcache_set(self::$connection, $id, $data, 0, $specificLifetime);
	}

	/**
	* Get value from server
	*
	* @param string $key
	* @return mixed
	*/
	public function load($id, $doNotTest = false)
	{
		if (self::$connection === NULL) return false;
		return @memcache_get(self::$connection, $id);
	}

	/**
	* Check if variable with this key is set on server
	*
	* @param string $key
	* @return bool
	*/
	public function test($id)
	{
		return ($this->load($id)!==false);
	}

	/**
	* Deletes variable from server
	*
	* @param string $key
	* @param int $timeout Timeout after which it will be deleted
	*/
	public function remove($id)
	{
		if (self::$connection === NULL) return ;
		memcache_delete(self::$connection, $id, 0);
	}

	public function clean($mode = CACHE_CLR_ALL, $tags = array())
	{
		if (self::$connection === NULL) return ;
		if ($mode == CACHE_CLR_ALL)
			memcache_flush(self::$connection);
	}

}
?>