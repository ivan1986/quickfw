<?php

require_once(QFWPATH.'/QuickFW/Auth.php');

class IndexController extends QuickFW_Auth
{
	public function __construct()
	{
		//echo QFW::$router->module.'.'.QFW::$router->controller.'.'.QFW::$router->action."<br>\n";
	}

	public function tttAction($p1='')
	{
		if (!$p1)
			return '';
		QFW::$view->assign('out', $p1);
		include dirname(dirname(__FILE__)).'/test/sub.php';
		return test\run(func_get_args(), 1);
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