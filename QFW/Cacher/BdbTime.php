<?php

require_once dirname(__FILE__).'/Bdb.php';

/**
 * Хранение в Berkeley DB ограниченное время
 */
class Cacher_BdbTime extends Cacher_Bdb implements Zend_Cache_Backend_Interface
{

	public function save($data, $id, $tags = array(), $specificLifetime = 3600)
	{
		$data = serialize(array(time()+$specificLifetime,$data));
		return parent::save($data, $id, $tags, $specificLifetime);
	}

	public function load($id, $doNotTest = false)
	{
		if (!$this->file) $this->conn();
		if (is_array($id))
			return parent::load($id, $doNotTest);
		$data = parent::load($id, $doNotTest);
		if (!$data)
			return false;
		$data=unserialize($data);
		if ($data[0]<time())
		{
			dba_delete($id,$this->file);
			return false;
		}
		return $data[1];
	}

}

?>
