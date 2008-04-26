<?php

class IndexController
{
	public function indexAction()
	{
		//die('test');
		//global $view,$db;
		$_SESSION['1']++;
		var_dump($_SESSION);
		echo session_id();
		//die();
		//$view->assign('ttt','111');
		//$db->select("SELECT * FROM album");
		
		return 'Корневое действие сайта, показывается на /, на /default, на /index и т.п.<br/>';
	}
	
	public function преведAction()
	{
		QFW::$view->mainTemplate='';
		return "медвед";
	}

	public function getTimestamp($action,$params)
	{
		return mktime();
	}
	
}

?>