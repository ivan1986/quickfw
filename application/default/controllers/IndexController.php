<?php

require_once(QFWPATH.'/QuickFW/Auth.php');

class IndexController extends QuickFW_Auth
{
	public function __construct()
	{
		//echo QFW::$router->module.'.'.QFW::$router->controller.'.'.QFW::$router->action."<br>\n";
	}

	public function indexAction()
	{
		QFW::$view->assign('title', 'Основная страница');
		return QFW::$view->fetch('index.html');
		return QFW::$view->fetch('b.html');
	}

	public function dinmenuAction()
	{
		return QFW::$view->fetch('dinmenu.html');
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

}

?>