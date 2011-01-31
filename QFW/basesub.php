<?php

namespace baseSub;

class QFW extends \QFW
{
	/** @var QuickFW_Router Роутер */
	static public $router;

	private function __construct() {}

	/**
	 * Инициализация основных объектов QFW
	 *
	 */
	static public function Init()
	{
		//подключаем модули и библиотеки
		self::modules();

		static::$router = new \QuickFW_Router(dirname(__FILE__), __NAMESPACE__);
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

function run($base, $uri)
{
	QFW::Init();
	Url::Init($base.'');
	$TS = new \TemplaterState(QFW::$view);
	return QFW::$router->subroute($uri, \QFW::$router->type);
}

?>
