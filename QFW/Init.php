<?php

	$config = QFW::config();

	if (isset($config['host']['encoding']))
		header("Content-Type: text/html; charset=".$config['host']['encoding']);
	if (isset($config['QFW']['catchFE']) && $config['QFW']['catchFE'])
		require QFWPATH.'/QuickFW/Error.php';

	require QFWPATH.'/QuickFW/Cache.php';
	require QFWPATH.'/QuickFW/Plugs.php';

	$templ = ucfirst($config['templater']['name']);
	$class = 'Templater_'.$templ;
	require (QFWPATH.'/Templater/'.$templ.'.php');
	$view = new $class(APPPATH,
		isset($config['templater']['def_tpl']) ? $config['templater']['def_tpl'] : '');

	require (QFWPATH.'/QuickFW/AutoDbSimple.php');
	$db = new QuickFW_AutoDbSimple($config['database']);

	$globalData = array();
	$libs = array();

	require (QFWPATH.'/QuickFW/Router.php');
	$router = new QuickFW_Router(APPPATH);

	class QFW
	{
		/**
		 * Глобальный массив данных
		 *
		 * @var array
		 */
		static public $globalData;

		/**
		 * Роутер
		 *
		 * @var QuickFW_Router
		 */
		static public $router;

		/**
		 * Конфигурация
		 *
		 * @var array
		 */
		static public $config;

		/**
		 * Шаблонизатор
		 *
		 * @var Templater_PlainView
		 */
		static public $view;

		/**
		 * Подключенные глобальные библиотеки
		 *
		 * @var array
		 */
		static public $libs;
		
		/**
		 * Подключение к базе данных
		 *
		 * @var DbSimple_Generic_Database
		 */
		static public $db;

		private function __construct() {}
		
		/**
		 * Инициализация конфига
		 */
		static public function config()
		{
			require QFWPATH.'/config.php';
			require APPPATH.'/default.php';

			if (!isset($_SERVER['HTTP_HOST']))
				die('$_SERVER[\'HTTP_HOST\'] NOT SET');
			$file = APPPATH.'/'.$_SERVER['HTTP_HOST'].'.php';
			if (is_file($file))
				require ($file);
			return $config;
		}

		static public function Init()
		{
			global $router, $db, $view, $globalData, $libs, $config;
			self::$globalData=$globalData;
			self::$router=$router;
			self::$config=$config;
			self::$view=$view;
			self::$libs=$libs;
			self::$db=$db;
		}
	}

	QFW::Init();
	
	if (isset($_REQUEST['JsHttpRequest']))
		require_once LIBPATH.'/JsHttpRequest.php';

?>