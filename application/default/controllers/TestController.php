<?php

class TestController
{
	public function indexModule()
	{
		global $router,$view;
		$aaa = QFW::$params->gCurModuleParams();
		return "<pre>".$view->render('index.tpl').print_r($aaa)
		."\nМодуль index - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath
		."\nЭто результат работы модуля Test с параметрами ". var_export(func_get_args(),true)."</pre>";
	}

	public function aModule()
	{
		global $router,$view;
		return $view->render('index2.tpl')
		."\nМодуль A - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath;
	}
	
	public function bModule()
	{
		global $router;
		return "\nМодуль B - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath;
	}

	public function getTimestamp($action,$params)
	{
		return mktime();
	}

}

?>