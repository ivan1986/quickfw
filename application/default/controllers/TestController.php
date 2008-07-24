<?php

class TestController
{
	public function indexModule()
	{
		global $router,$view;
		return "<pre>".$view->render('b.html')
		."\nМодуль index - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath
		."\nЭто результат работы модуля Test с параметрами ". var_export(func_get_args(),true)."</pre>";
	}

	public function aModule()
	{
		global $router,$view;
		return $view->render('b.html')
		."\nМодуль A - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath;
	}

	public function bModule()
	{
		global $router,$view;
		return $view->render('b.html')."\nМодуль B - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath;
	}

	/*public function CacheInfo($action,$params)
	{
		return array(
			'Cacher' => getCache(),
			'id' => 'Test_index',
		);
	}*/

}

?>