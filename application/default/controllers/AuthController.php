<?php

class AuthController
{
	
	public function indexAction()
	{
		global $view;
		/* @var $view SeriousDron_Smarty */
		
		$view->assign('contentTpl', 'auth.tpl');
		$view->display();
		exit(0);
	}
	
}

?>