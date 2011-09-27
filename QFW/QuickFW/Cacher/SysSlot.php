<?php

class QuickFW_Cacher_SysSlot extends Dklab_Cache_Frontend_Slot
{
	public function __construct($data, $time=false)
	{
		parent::__construct('QFW_'.$data, $time);
	}

	protected function _getBackend()
	{
		return Cache::get('Null');
		//return Cache::get('Apc');
	}

	public static function is_use($where=false)
	{
		// используются 'MCA', 'autoload', 'config'
		return false;
		/*if ($where == 'MCA')
			return false;
		if ($where == 'autoload')
			return false;
		if ($where == 'config')
			return false;
		return false;*/
	}
}
