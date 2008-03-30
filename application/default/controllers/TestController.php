<?php

class TestController
{
	public function IndexModule()
	{
		return "Это результат работы модуля Test с параметрами ". var_export(func_get_args(),true);
	}
	
	public function getTimestamp($action,$params)
	{
		return mktime();
	}

}

?>