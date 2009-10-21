<?php

class TestController
{
	public function __construct()
	{
		echo 1;
	}

	public function indexBlock()
	{
		return "<pre>".QFW::$view->render('b.html')
		."\nБлок index - ".QFW::$router->UriPath.' '.QFW::$router->CurPath.' '.QFW::$router->ParentPath
		."\nЭто результат работы блока index с параметрами ". print_r(func_get_args(),true)."</pre>";
	}

	public function aBlock()
	{
		return QFW::$view->render('b.html')
		."\nБлок A - ".QFW::$router->UriPath.' '.QFW::$router->CurPath.' '.QFW::$router->ParentPath;
	}

	public function bBlock()
	{
		return QFW::$view->render('b.html')."\nБлок B - ".
			QFW::$router->UriPath.' '.QFW::$router->CurPath.' '.QFW::$router->ParentPath;
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