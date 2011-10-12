<?php

class Cacher_Bdb implements Zend_Cache_Backend_Interface
{
	protected $file = NULL;
	protected $opt = array();

	public function __construct()
	{
	}

	public function setDirectives($directives)
	{
		$this->opt = array(
			'file' => isset($directives['file']) ? $directives['file'] : 'cache',
			'dba'  => isset($directives['dba'])  ? $directives['dba']  : 'db4',
			'type' => isset($directives['type']) ? $directives['type'] : 'cd',
		);
	}
	
	private function conn()
	{
		$this->file=dba_open(TMPPATH.'/'.$this->opt['file'].'.'.$this->opt['dba'], $this->opt['type'], $this->opt['dba']);
	}

	public function save($data, $id, $tags = array(), $specificLifetime = 3600)
	{
		if (!$this->file) $this->conn();
		return dba_replace($id,serialize($data),$this->file);
	}

	public function load($id, $doNotTest = false)
	{
		if (!$this->file) $this->conn();
		if (!is_array($id))
			return unserialize(dba_fetch($id,$this->file));
		$x = array();
		foreach($id as $v)
			$x[$v] = $this->load($v);
		return $x;
	}

	public function test($id)
	{
		return ($this->load($id)!==false);
	}

	public function remove($id)
	{
		if (!$this->file) $this->conn();
		return dba_delete($id,$this->file);
	}

	public function clean($mode = CACHE_CLR_ALL, $tags = array())
	{
		if (!$this->file) $this->conn();
		if (!$key=dba_firstkey($this->file))
			return;
		do {
			if ($mode == CACHE_CLR_ALL)
				dba_delete($key,$this->file);
		} while($key=dba_nextkey($this->file));
		dba_optimize($this->file);
		dba_sync($this->file);
	}

}
?>