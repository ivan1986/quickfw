<?php

class TestController
{
	public function __construct()
	{
		echo 1;
	}

	public function indexBlock()
	{
		global $router,$view;
		return "<pre>".$view->render('b.html')
		."\nБлок index - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath
		."\nЭто результат работы блока index с параметрами ". var_export(func_get_args(),true)."</pre>";
	}

	public function aBlock()
	{
		global $router,$view;
		return $view->render('b.html')
		."\nБлок A - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath;
	}

	public function bBlock()
	{
		global $router,$view;
		return $view->render('b.html')."\nБлок B - ".$router->UriPath.' '.$router->CurPath.' '.$router->ParentPath;
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