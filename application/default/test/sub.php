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

/**
 * Вызывает саброутинг
 *
 * @param array $args результат func_get_args()
 * @param integer $count сколько использовано в экшене
 * @return string результат
 */
function run($args, $count)
{
	$base = \Url::base(\Url::A('')->intern());
	while($count--)
		$base.= \QuickFW_Router::PATH_SEPARATOR.array_shift($args);
	$uri = join(\QuickFW_Router::PATH_SEPARATOR, $args);
	QFW::Init();
	Url::Init($base.\QuickFW_Router::PATH_SEPARATOR);
	$TS = new \TemplaterState(QFW::$view);
	return QFW::$router->subroute($uri, \QFW::$router->type);
}

?>
