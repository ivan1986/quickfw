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
			require (LIBPATH.'/Cacher/'.$name.'.php');
			$c=new $cl;
		}
		return $c;
	}

	require (LIBPATH.'/QuickFW/config.php');
	
	if (isset($_SERVER['HTTP_HOST']))
	{
		$file=APPPATH.'/config/'.$_SERVER['HTTP_HOST'].'.php';
		if (is_file($file))
		{
			require ($file);
		}
		else
		{
			if (is_file(APPPATH.'/config/default.php'))
				require APPPATH.'/config/default.php';
		}
	}

	//$manager = new QuickFW_Manager();
	//$cacher = new QuickFW_Cacher_Memcached();
		
	if (isset($config['host']['encoding']))
		header("Content-Type: text/html; charset=".$config['host']['encoding']);

	if (isset($config['state']['autosession']) && $config['state']['autosession'])
	{
		require (LIBPATH.'/QuickFW/Session.php');
		$Session = new QuickFW_Session();
	}
	
	$templ = ucfirst($config['templater']['name']);
	$class = 'QuickFW_'.$templ;
	require (LIBPATH.'/QuickFW/'.$templ.'.php');
	$view = new $class(ROOTPATH .'/application', 
		isset($config['state']['def_tpl'])?$config['state']['def_tpl']:"");
	
	require (LIBPATH.'/QuickFW/AutoDbSimple.php');
	$db = new QuickFW_AutoDbSimple( $config['database']['username'],
	                                $config['database']['password'],
	                                $config['database']['dbname'],
	                                $config['database']['prefix'],
	                                $config['database']['type'],
	                                $config['database']['host'],
	                                $config['database']['encoding']
	                              );

	require (LIBPATH.'/QuickFW/Auth.php');
	$auth = new QuickFW_Auth();

?>