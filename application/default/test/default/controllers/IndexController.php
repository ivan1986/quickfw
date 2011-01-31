<?php
namespace test;

require_once(QFWPATH.'/QuickFW/Auth.php');

class IndexController extends \QuickFW_Auth
{
	public function __construct()
	{
	}

	public function indexAction()
	{
		QFW::$view->assign('in', 'in');
		return QFW::$view->fetch('index.php');
	}

}

?>