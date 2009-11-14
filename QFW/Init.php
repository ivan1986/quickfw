<?php

require QFWPATH.'/QuickFW/Cache.php';
require QFWPATH.'/QuickFW/Plugs.php';
require QFWPATH.'/QuickFW/AutoDbSimple.php';
require QFWPATH.'/QuickFW/Router.php';

class QFW
{
	/** @var array Глобальный массив данных */
	static public $globalData;

	/** @var QuickFW_Router Роутер */
	static public $router;

	/** @var array Конфигурация */
	static public $config;

	/** @var Templater_PlainView Шаблонизатор */
	static public $view;

	/** @var array Подключенные глобальные библиотеки */
	static public $libs;

	/** @var DbSimple_Generic_Database Подключение к базе данных */
	static public $db;

	/** @var mixed|false Данные о пользователе */
	static public $userdata;

	private function __construct() {}

	/**
	 * Инициализация конфига
	 *
	 * <br>возвращает конфигурацию, специфичную для текущего хоста
	 *
	 * @return array конфигурация на этом хосте
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

	/**
	 * Инициализация основных объектов QFW
	 *
	 */
	static public function Init()
	{
		self::$config = self::config();

		//Библиотеки
		self::$libs = array();
		//глобальный массив
		self::$globalData = array();
		//Данные о пользователе
		self::$userdata = false;

		//Подключаем шаблонизатор
		$templ = ucfirst(self::$config['templater']['name']);
		$class = 'Templater_'.$templ;
		require (QFWPATH.'/Templater/'.$templ.'.php');
		self::$view = new $class(APPPATH,
			isset(self::$config['templater']['def_tpl']) ? self::$config['templater']['def_tpl'] : '');

		//Инициализируем класс базы данных
		self::$db = new QuickFW_AutoDbSimple(self::$config['database']);

		//выставляем заголовок с нужной кодировкой
		if (isset(self::$config['host']['encoding']))
			header("Content-Type: text/html; charset=".self::$config['host']['encoding']);
		//Включаем обработку фатальных ошибок, если в конфиге указано
		if (isset(self::$config['QFW']['catchFE']) && self::$config['QFW']['catchFE'])
			require QFWPATH.'/QuickFW/Error.php';

		self::$router = new QuickFW_Router(APPPATH);

	}
}

QFW::Init();

if (isset($_REQUEST['JsHttpRequest']))
	require_once LIBPATH.'/JsHttpRequest.php';

?>
