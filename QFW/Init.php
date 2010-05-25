<?php

class QFW
{
	/** @var array Глобальный массив данных */
	static public $globalData = array();

	/** @var QuickFW_Router Роутер */
	static public $router;

	/** @var array Конфигурация */
	static public $config;

	/** @var Templater_PlainView Шаблонизатор */
	static public $view;

	/** @var array Подключенные глобальные библиотеки */
	static public $libs = array();

	/** @var DbSimple_Generic_Database|false Подключение к базе данных */
	static public $db = false;

	/** @var mixed|false Данные о пользователе */
	static public $userdata = false;

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

		//выставляем заголовок с нужной кодировкой
		if (!empty(self::$config['host']['encoding']))
			header("Content-Type: text/html; charset=".self::$config['host']['encoding']);

		//Инициализируем класс базы данных
		require LIBPATH.'/DbSimple/Connect.php';
		self::$db = new DbSimple_Connect(self::$config['database']);

		//Подключаем шаблонизатор
		$templ = ucfirst(self::$config['templater']['name']);
		$class = 'Templater_'.$templ;
		require QFWPATH.'/Templater/'.$templ.'.php';
		self::$view = new $class(APPPATH,
			isset(self::$config['templater']['def_tpl']) ? self::$config['templater']['def_tpl'] : '');

		//подключаем модули и библиотеки
		self::modules();

		require QFWPATH.'/QuickFW/Router.php';
		self::$router = new QuickFW_Router(APPPATH);

		//хелпер для урлов (зависит от QuickFW_Router)
		require QFWPATH.'/QuickFW/Url.php';
		Url::Init();
	}

	/**
	 * Инициализирует необязательные модули
	 * <br>в зависимости от настроек конфигов
	 */
	static public function modules()
	{
		//Включаем обработку фатальных ошибок, если в конфиге указано
		if (!empty(self::$config['error']))
		{
			require_once QFWPATH.'/QuickFW/Error.php';
			foreach(self::$config['error'] as $handler)
			QFW_Listener::addFromConfig($handler);
		}

		//автолоад
		if (!empty(self::$config['QFW']['autoload']))
		{
			require QFWPATH.'/QuickFW/Autoload.php';
			Autoload::Init(self::$config['QFW']['autoload']);
		}

		//JsHttpRequest
		if (isset($_REQUEST['JsHttpRequest']))
		{
			require_once LIBPATH.'/JsHttpRequest.php';
			//QFW::$libs['JsHttpRequest'] для совместимости	со старым вариантом
			self::$ajax = QFW::$libs['JsHttpRequest'] = new
				JsHttpRequest(self::$config['host']['encoding']);
			//устанавливаем пустой главный шаблон
			self::$view->mainTemplate = '';
		}
	}
}

QFW::Init();

?>
