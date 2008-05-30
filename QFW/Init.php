<?php
	
	if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI']='/';
	define ('APPPATH', ROOTPATH . '/application');
	define ('TMPPATH', ROOTPATH . '/tmp');
	
	function __autoload($classname)
	{
		$file=strtr($classname,'_','/');
		require (APPPATH.'/'.$file.'.php');
	}

	//нужна для того, что сессии используют кешер и они записывают данные
	//после уничтожения всех обьектов, кешер пересоздается заново
	//для записи сессий
	function getCache()
	{
		global $config;
		static $name;
		static $c;
		if (!$name)
			$name=ucfirst($config['cacher']['module']);
		if (!$c)
		{
			$cl='Cacher_'.$name;
			require (QFWPATH.'/Cacher/'.$name.'.php');
			$c=new $cl;
		}
		return $c;
	}

	require (QFWPATH.'/config.php');
	
	if (isset($_SERVER['HTTP_HOST']))
	{
		$file=APPPATH.'/'.$_SERVER['HTTP_HOST'].'.php';
		if (is_file($file))
		{
			require ($file);
		}
		else
		{
			if (is_file(APPPATH.'/default.php'))
				require APPPATH.'/default.php';
		}
	}

	if (isset($config['host']['encoding']))
		header("Content-Type: text/html; charset=".$config['host']['encoding']);

	$templ = ucfirst($config['templater']['name']);
	$class = 'QuickFW_'.$templ;
	require (QFWPATH.'/QuickFW/'.$templ.'.php');
	$view = new $class(ROOTPATH .'/application', 
		isset($config['state']['def_tpl'])?$config['state']['def_tpl']:"");
	
	require (QFWPATH.'/QuickFW/AutoDbSimple.php');
	$db = new QuickFW_AutoDbSimple( $config['database']['username'],
	                                $config['database']['password'],
	                                $config['database']['dbname'],
	                                $config['database']['prefix'],
	                                $config['database']['type'],
	                                $config['database']['host'],
	                                $config['database']['encoding']
	                              );

	$globalData = array();
	$libs = array();

?>