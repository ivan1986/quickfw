<?php
	define ('DOC_ROOT', realpath(dirname(__FILE__).'../www'));
	define ('ROOTPATH', realpath(dirname(__FILE__).'/../'));
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');
	
	require (QFWPATH.'/Init.php');

	class QFW
	{
		static public $globalData;
		static public $router;
		static public $view;
		static public $libs;
		static public $db;

		private function __construct() {}

		static public function Init()
		{
			global $router,$db,$view,$globalData,$libs;
			self::$globalData=$globalData;
			self::$router=$router;
			self::$view=$view;
			self::$libs=$libs;
			self::$db=$db;
		}
	}
	
	require (QFWPATH.'/QuickFW/Router.php');
	$router = new QuickFW_Router(ROOTPATH . '/application');

	QFW::Init();
	
	array_shift($argv);
	$argv=join('/',$argv);
	
	$router->route($argv,'Cli');
?>