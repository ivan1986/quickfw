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
	function &getCache($backend='',$tags=false,$namespace='')
	{
		global $config;
		static $cachers=array();
		if ($backend=='')
			$backend=ucfirst($config['cacher']['module']);
		$key=$backend.($tags?'1':'0').$namespace;
		if (isset($cachers[$key]))
			return $cachers[$key];
		
		$cl='Cacher_'.$backend;
		require_once(QFWPATH.'/Cacher/'.$backend.'.php');
		$c=new $cl;
		if ($namespace!='')
		{
			require_once(QFWPATH.'/QuickFW/Cacher/Namespace.php');
			$c=new Dklab_Cache_Backend_NamespaceWrapper($c);
		}
		if ($tags)
		{
			require_once(QFWPATH.'/QuickFW/Cacher/TagEmu.php');
			$c=new Dklab_Cache_Backend_TagEmuWrapper($c);
		}
		$cachers[$key]=&$c;
		return $cachers[$key];
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
	$class = 'Templater_'.$templ;
	require (QFWPATH.'/Templater/'.$templ.'.php');
	$view = new $class(ROOTPATH .'/application', 
		isset($config['templater']['def_tpl'])?$config['templater']['def_tpl']:"");
	
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