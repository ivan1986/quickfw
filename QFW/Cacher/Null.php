<?php

/**
 * Null cache interface
 */
class Cacher_Null implements Zend_Cache_Backend_Interface
{
	public function setDirectives($directives)
	{
	}

	public function save($data, $id, $tags = array(), $specificLifetime = 3600)
	{
	}

	public function load($id, $doNotTest = false)
	{
		return false;
	}

	public function test($id)
	{
		return false;
	}

	public function remove($id)
	{
		return true;
	}

	public function clean($mode = CACHE_CLR_ALL, $tags = array())
	{
	}

}
