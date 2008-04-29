<?php
	//Для отладки без сервера
	if (!isset($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = getcwd();
	define ('DOC_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));
	define ('ROOTPATH', realpath($_SERVER['DOCUMENT_ROOT'] . '/../'));
	define ('LIBPATH', ROOTPATH . '/library');
	
	require (LIBPATH.'/Init.php');

	class QFW
	{
		static public $router;
		static public $params;
		static public $view;
		static public $db;

		private function __construct() {}

		static public function Init()
		{
			global $router,$db,$view,$params;
			self::$params=$params;
			self::$router=$router;
			self::$view=$view;
			self::$db=$db;
		}
	}
	
	require (LIBPATH.'/QuickFW/Router.php');
	$router = new QuickFW_Router(ROOTPATH . '/application');

	QFW::Init();
	
	$router->route();
?>