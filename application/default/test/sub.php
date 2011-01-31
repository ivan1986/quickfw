<?php

namespace test;

require_once QFWPATH.'/basesub.php';

class QFW extends \baseSub\QFW
{
	/** @var QuickFW_Router Роутер */
	static public $router;

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

}

class Url extends \baseSub\Url {}

function run($base, $uri)
{
	QFW::Init();
	Url::Init($base.'');
	$TS = new \TemplaterState(QFW::$view);
	return QFW::$router->subroute($uri, \QFW::$router->type);
}

?>
