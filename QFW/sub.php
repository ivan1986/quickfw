<?php

namespace test;

class QFW extends \QFW
{
	/** @var QuickFW_Router Роутер */
	static public $router;

	/**
	 * Инициализация основных объектов QFW
	 *
	 */
	static public function Init()
	{
		static::$router = new \QuickFW_Router(dirname(__FILE__), __NAMESPACE__);
	}

}

class Url extends \Url { protected static $config;}

/**
 * Вызывает саброутинг
 *
 * @param array $args результат func_get_args()
 * @param integer $count сколько использовано в экшене
 * @return string результат
 */
function run($args, $count)
{
	$base = join(\QuickFW_Router::PATH_SEPARATOR, \array_slice($args, 0, $count));
	$uri = join(\QuickFW_Router::PATH_SEPARATOR, \array_slice($args, $count));
	QFW::Init();
	Url::Init(\Url::A($base)->getBase());
	$TS = new \TemplaterState(QFW::$view);
	return QFW::$router->subroute($uri, \QFW::$router->type);
}

?>
