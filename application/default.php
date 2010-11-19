<?php

setlocale(LC_ALL, 'ru_RU.UTF-8');
mb_internal_encoding("UTF-8");

/* настройки хоста - установка Content-Type: text/html; charset=encoding */
$config['host']=array(
	'encoding' => 'utf-8',
	'lang' => 'ru_RU',
	'logpath' => ROOTPATH.'/log',
);

/* Настройки дефолтового MCA */
$config['default']=array(
	'module'    => 'default',
	'controller' => 'index',
	'action'    => 'index',
);

/*
 * Настройки коннекта к базе данных в формате
 * driver:[//[user[:pass]@]host[:port]]/base[?enc=codepage][&persist=(0|1)][&timeout=(sec|0)]
 */
$config['database']='mypdo://root@localhost/smspr?enc=utf8';

/* Настройки перенаправления */
$config['redirection']=array(
	'baseUrl'          => '/',
	'useIndex'         => false,
	'defExt'           => '',	//или пусто или .html например
	'useRewrite'       => true,
	'useBlockRewrite'  => false,
	'delDef'           => true,
);

/**
 * Настройки обработчика ошибок
 */
$config['error'] = array();
$config['error'][] = array(
	'name' => 'mail',
	'RemoveDups' => 300, //секунд или false
	'options' => array(
		'to' => 'ivan1986@localhost',
		'whatToSend' => 65535, // LOG_ALL (look in TextNotifier)
		'subjPrefix' => '[ERROR] ',
		'charset' => 'UTF-8',
	),
);
$config['error'] = false;

/**
 * Настройки кешера (класс бекенда и дополнительные параметры, если есть)
 *
 * @deprecated лучше юзайте новый кешер
 */
$config['cacher']=array(
	'module' => 'Bdb',
	'options' => array(
		'file'=>'ttt',
	),
);
/**
 * Настройки кешеров
 * Массив из названий, обязателен default
 */
$config['cache'] = array(
	'default' => array(
		'module' => 'Bdb',
		'namespace' => '',
		'tags' => false,
	),
	/*'MCA' => array(
		'module' => 'Xcache',
		'namespace' => '',
		'tags' => false,
	),*/
);

/**
 * Флаги, влияющие на поведение всяких вещей
 */
$config['QFW'] = array(
	'release' => false, /* статус проекта на данном хосте - отладка и всякие быстрые компиляции */
	'cacheSessions' => false, /* Хранить сессии в кеше, не использовать стандартный механизм */
	'autoload' => true, /* включить автолоад false|true|string */
	'auto404' => false, /* не перенаправлять на дефолтовый контроллер все запросы  */
	'addCSSXml' => false, /* addJS и addCSS выводят теги в XML  */
);

/* Шаблонизатор - имя класса + дефолтовый шаблон */
/*$config['templater']= array(
	'name'      => 'PlainView',
	'def_tpl'   => 'main.php',
);*/

$config['templater']= array(
	'name'      => 'Proxy',
	'def_tpl'   => 'main.php',
	'exts' => array(
		'tpl' => 'Smarty',
		'html' => 'PlainView',
	),
);

$config['jabber'] = array(
	'host' => 'webim.qip.ru',
	'port' => 5222,
	'server' => 'qip.ru',
	'user' => 'subaaaaaa',
	'pass' => '123456',
	'resource' => 'bot',
);

$config['log'] = array(
	'log' => 'error',
	'critical' => 'xmpp://ivan1986@jabber.ru',
	'error' => 'mailto:ivan1986@list.ru',
);

$config['admin'] = array(
	'login' => 'root',
	'passw' => 'root',
);

$config['cruise'] = array(
	'restart' => 24*60*60,	//через сколько рестартовать или false если никогда
	'interval' => 30,	//как часто проверять события
	//ключ передается в роурет, значение - время и +/- рандом времени
	'actions' => array(),
);

/* деквотатор, включите если нужно на хостинге */
/*
function strips(&$el) {
	if (is_array($el))
		foreach($el as $k=>$v)
			strips($el[$k]);
	else $el = stripslashes($el);
}
if (get_magic_quotes_gpc()) {
	strips($_GET);
	strips($_POST);
	strips($_COOKIE);
	strips($_REQUEST);
	if (isset($_SERVER['PHP_AUTH_USER'])) strips($_SERVER['PHP_AUTH_USER']);
	if (isset($_SERVER['PHP_AUTH_PW']))   strips($_SERVER['PHP_AUTH_PW']);
}
set_magic_quotes_runtime(0);
/**/

?>