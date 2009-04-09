<?php

class Cacher_Memcache implements Zend_Cache_Backend_Interface
{
	protected $mc;

	public function __construct()
	{
		$this->mc = new Memcache;
	}

	public function setDirectives($directives)
	{
		if(!isset($directives['servers'])){
			$this->mc->addServer('localhost','11211');
			return;
		}

		foreach($directives['servers'] as $server)
			$this->mc->addServer($server['host'], $server['port']);
	}

	public function addServer($host='localhost', $port=11211)
	{
		return $this->mc->addServer($host, $port);
	}

	public function save($data, $id, $tags = array(), $specificLifetime = 3600)
	{
		$this->mc->set($id, $data, 0, $specificLifetime);
	}

	public function load($id, $doNotTest = false)
	{
		return $this->mc->get($id);
	}

	public function test($id)
	{
		return ($this->load($id)!==false);
	}

	public function getStats()
	{
		return $this->mc->getExtendedStats();
	}

	public function remove($id)
	{
		$this->mc->delete($id, 0);
	}

	public function clean($mode = CACHE_CLR_ALL, $tags = array())
	{
		if ($mode == CACHE_CLR_ALL)
			$this->mc->flush();
	}
}

?>
