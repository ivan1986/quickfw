<?php

namespace test;

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

	/** @var DbSimple_Database|false Подключение к базе данных */
	static public $db = false;

	/** @var mixed|false Данные о пользователе */
	static public $userdata = false;

	/** @var JsHttpRequest|jQuery|false JsHttpRequest или jQuery, если был выполнени Ajax запрос */
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
		self::$config = \QFW::$config;

		self::$db = \QFW::$db;

		self::$view = \QFW::$view;

		//подключаем модули и библиотеки
		self::modules();

		require_once QFWPATH.'/QuickFW/Router.php';
		self::$router = new \QuickFW_Router(dirname(__FILE__), __NAMESPACE__);
	}

	/**
	 * Инициализирует необязательные модули
	 * <br>в зависимости от настроек конфигов
	 */
	static public function modules()
	{
		//TODO настройка автолоада
	}

}

class Url extends \Url
{
	public static function Init($base)
	{
		static::$config = QFW::$config['redirection'];
		static::$config['delDef'] = QFW::$config['redirection']['delDef'];
		static::$config['baseUrl'] = static::$config['base'] = $base;
		static::$config['ext'] = (\QuickFW_Router::PATH_SEPARATOR == '/' ? '/' : '');
	}

	/** @var array QFW::$config['redirection'] */
	protected static $config;

}

class run
{
	static public function run($base, $uri)
	{
		Url::Init($base.'');
		$TS = new \TemplaterState(QFW::$view);
		$data = QFW::$router->r($uri);
		return $data;
	}
}

QFW::Init();

?>
