<?php
require('Abstract.php');

class Cacher_File extends Cacher_Abstract
{
	public function __construct()
	{
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
		file_put_contents(TMPPATH.'/cache/'.$key, serialize($value));
	}
	
	/**
	* Get value from server
	*
	* @param string $key
	* @return mixed
	*/
	public function get($key)
	{
		if (!is_file(TMPPATH.'/cache/'.$key)) return false;
		return unserialize(file_get_contents(TMPPATH.'/cache/'.$key));
	}
	
	/**
	* Check if variable with this key is set on server
	*
	* @param string $key
	* @return bool
	*/
	public function incache($key)
	{
		return is_file(TMPPATH.'/cache/'.$key);
	}
	
	/**
	* Deletes variable from server
	*
	* @param string $key
	* @param int $timeout Timeout after which it will be deleted
	*/
	public function delete($key, $timeout = 0)
	{
		unlink(TMPPATH.'/cache/'.$key);
	}

}
?>