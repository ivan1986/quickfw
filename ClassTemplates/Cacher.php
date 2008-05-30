<?php

abstract class Cacher_Class
{
	/*abstract public function save($data, $id, $lifetime);
	abstract public function load($id);
	abstract public function remove($id);*/
	
	abstract public function set($key, $value);
	abstract public function get($key);
	abstract public function incache($key);
	abstract public function delete($key);
	
}

?>