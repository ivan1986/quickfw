<?php

require_once(QFWPATH.'/QuickFW/Auth.php');

class IndexController extends QuickFW_Auth
{
	public function indexAction()
	{
		//$x = Cache::slot('Test',1);
		return QFW::$view->fetch('b.html');

		return 'Корневое действие сайта, показывается на /, на /default, на /index и т.п.<br/>';
	}

	public function testBlock()
	{
		echo 2;
	}

	public function преведAction()
	{
		QFW::$view->mainTemplate='';
		return "медвед";
	}

	/*public function CacheInfo($action,$params)
	{
		return array(
			'Cacher' => getCache(),
			'id' => 'ALL'.$action,
			//'full'=>1,
		);
	}*/

}

?>