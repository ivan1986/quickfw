<?php

	require QFWPATH.'/config.php';
	require APPPATH.'/default.php';

	if (!isset($_SERVER['HTTP_HOST']))
		die('$_SERVER[\'HTTP_HOST\'] NOT SET');
	$file=APPPATH.'/'.$_SERVER['HTTP_HOST'].'.php';
	if (is_file($file))
		require ($file);

	if (isset($config['host']['encoding']))
		header("Content-Type: text/html; charset=".$config['host']['encoding']);

	require QFWPATH.'/QuickFW/Cache.php';
	require QFWPATH.'/QuickFW/Block.php';
	require QFWPATH.'/QuickFW/Plugs.php';

	$templ = ucfirst($config['templater']['name']);
	$class = 'Templater_'.$templ;
	require (QFWPATH.'/Templater/'.$templ.'.php');
	$view = new $class(APPPATH,
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

	require (QFWPATH.'/QuickFW/Router.php');
	$router = new QuickFW_Router(APPPATH);

	class QFW
	{
		static public $globalData;
		static public $router;
		static public $config;
		static public $view;
		static public $libs;
		static public $db;

		private function __construct() {}

		static public function Init()
		{
			global $router,$db,$view,$globalData,$libs,$config;
			self::$globalData=$globalData;
			self::$router=$router;
			self::$config=$config;
			self::$view=$view;
			self::$libs=$libs;
			self::$db=$db;
		}
	}

	QFW::Init();

?>