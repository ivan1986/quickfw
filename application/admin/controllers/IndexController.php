<?php

require (LIBPATH.'/QuickFW/Auth.php');

class IndexController extends QuickFW_Auth 
{
	function __construct()
	{
		if(!parent::__construct())
		{
			QFW::$view->assign('content',$this->login());
			QFW::$view->displayMain();
			die();
		}
	}
	
	function login()
	{
		return QFW::$view->fetch('auth.tpl');
	}

	public function indexAction()
	{
		echo 'Главная страница админки. Защищенная зона';
	}
}

?>