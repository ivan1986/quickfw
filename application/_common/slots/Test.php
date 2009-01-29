<?php

class Slot_Test extends Dklab_Cache_Frontend_Slot
{
	public function __construct($id)
	{
		parent::__construct('test_'.$id,3600*24);
	}
	
	protected function _getBackend()
	{
		return Cache::get();
	}
}

?>