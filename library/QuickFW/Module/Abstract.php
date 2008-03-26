<?php

abstract class QuickFW_Module_Abstract
{
	abstract public function getTimestamp($action,$params);
	//TO DO: добавить дефолтовое время кеширования вместо абстрактного
}

?>