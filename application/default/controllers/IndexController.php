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
		return QFW::$view->fetch('index.php');
	}

	public function dinmenuAction()
	{
		return QFW::$view->fetch('dinmenu.php');
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