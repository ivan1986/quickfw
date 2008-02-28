<?php

abstract class Admin_Controller_Abstract
{
	function __construct()
	{
		global $auth, $router, $view;
		if (!$auth->isAuthorized())
		{
			return $router->route('/default/auth');
		}
		$view->assign('admin', true);
		$view->mainTemplate = 'admin/main.tpl';
	}
}

?>