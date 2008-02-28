<?php

class IndexController
{
	public function indexAction()
	{
		global $view,$db;
		$_SESSION['1']++;
		var_dump($_SESSION);
		echo session_id();
		$view->assign('ttt','111');
		$db->select("SELECT * FROM album");
		return 'Корневое действие сайта, показывается на /, на /default, на /index и т.п.<br/>';
	}

	public function getTimestamp($action,$params)
	{
		return mktime();
	}
	
}

?>