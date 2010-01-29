<?php

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

	/** @var JsHttpRequest|false JsHttpRequest, если был выполнени Ajax запрос */
	static public $ajax = false;

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

		require QFWPATH.'/QuickFW/Cache.php';
		require QFWPATH.'/QuickFW/Plugs.php';

		//Библиотеки
		self::$libs = array();
		//глобальный массив
		self::$globalData = array();
		//Данные о пользователе
		self::$userdata = false;

		//Подключаем шаблонизатор
		$templ = ucfirst(self::$config['templater']['name']);
		$class = 'Templater_'.$templ;
		require QFWPATH.'/Templater/'.$templ.'.php';
		self::$view = new $class(APPPATH,
			isset(self::$config['templater']['def_tpl']) ? self::$config['templater']['def_tpl'] : '');

		//Если запрос через JsHttp, то инициализируем библиотеку
		//и устанавливаем пустой главный шаблон
		if (isset($_REQUEST['JsHttpRequest']))
		{
			require_once LIBPATH.'/JsHttpRequest.php';
			//QFW::$libs['JsHttpRequest'] для совместимости	со старым вариантом
			self::$ajax = QFW::$libs['JsHttpRequest'] = new JsHttpRequest('utf-8');
			self::$view->mainTemplate = '';
		}

		require LIBPATH.'/DbSimple/Connect.php';
		//Инициализируем класс базы данных
		self::$db = new DbSimple_Connect(self::$config['database']);

		//выставляем заголовок с нужной кодировкой
		if (!empty(self::$config['host']['encoding']))
			header("Content-Type: text/html; charset=".self::$config['host']['encoding']);
		//Включаем обработку фатальных ошибок, если в конфиге указано
		if (!empty(self::$config['QFW']['catchFE']))
			require QFWPATH.'/QuickFW/Error.php';

		require QFWPATH.'/QuickFW/Router.php';
		self::$router = new QuickFW_Router(APPPATH);

	}
}

QFW::Init();

?>
