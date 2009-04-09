<?php

/**
 * Dklab_Cache - сохраняем начальные копирайты, но код переписан :)
 */
class Dklab_Cache_Backend_NamespaceWrapper implements Zend_Cache_Backend_Interface 
{
	private $_backend = null;
	private $_namespace = null;
	
	public function __construct(Zend_Cache_Backend_Interface $backend, $namespace)
	{
		$this->_backend = $backend;
		$this->_namespace = $namespace;
	}
	
	public function setDirectives($directives)
	{
		return $this->_backend->setDirectives($directives);
	}
	
	public function load($id, $doNotTest = false)
	{
		$id = is_array($id) ? array_map(array($this, '_mangleId'), $id) : $this->_mangleId($id);
		$data = $this->_backend->load($id, $doNotTest);
		
		if (!is_array($id) || !is_array($data))
			return $data;
		$d = array();
		$l = strlen($this->_namespace) + 1;
		foreach($data as $k=>$v)
			$d[substr($k, $l)] = $v;
		return $d;
	}
	public function test($id)
	{
		return $this->_backend->test($this->_mangleId($id));
	}
	
	public function save($data, $id, $tags = array(), $specificLifetime = false)
	{
		$tags = array_map(array($this, '_mangleId'), $tags);
		return $this->_backend->save($data, $this->_mangleId($id), $tags, $specificLifetime);
	}
	public function remove($id)
	{
		return $this->_backend->remove($this->_mangleId($id));
	}
	
	public function clean($mode = CACHE_CLR_ALL, $tags = array())
	{
		$tags = array_map(array($this, '_mangleId'), $tags);
		return $this->_backend->clean($mode, $tags);
	}
	
	private function _mangleId($id) {return $this->_namespace . "_" . $id;}
}

?>