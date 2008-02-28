<?php

abstract class QuickFW_Module_Abstract
{
	abstract public function getTimestamp($action,$params);
	//TO DO: добавить дефолтовое время кеширования вместо абстрактного
	
	static function isSecure(&$smarty = NULL)
	{
		return true;
	}
	
	static function isTrusted(&$smarty = NULL)
	{
		return false; //Don't using modules for {include_php ...}
	}
}

?>