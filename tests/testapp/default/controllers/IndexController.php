<?php

require_once(QFWPATH.'/QuickFW/Auth.php');

class IndexController extends QuickFW_Auth
{
	public function __construct()
	{
	}

	public function indexAction()
	{
		return 'Корневое действие сайта';
	}

}

?>