<?php
	//Для отладки без сервера
	if (!isset($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = getcwd();
	define ('DOC_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));
	define ('ROOTPATH', realpath($_SERVER['DOCUMENT_ROOT'] . '/../'));
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
	
	$router->route();
?>