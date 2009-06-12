<?php

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
 * driver:[//[user[:pass]@]host[:port]]base[?enc=codepage][&persist=(0|1)][&timeout=(sec|0)]
 */
$config['database']='mypdo://root@localhost/smspr?enc=utf8';

/* Настройки перенаправления */
$config['redirection']=array(
	'baseUrl'          => '/',
	'useIndex'         => false,
	'defExt'           => '',	//или пусто или .html например
	'useRewrite'       => true,
	'useBlockRewrite'  => false,
);

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
);

/**
 * Флаги, влияющие на поведение всяких вещей
 */
$config['QFW'] = array(
	'release' => false, /* статус проекта на данном хосте - отладка и всякие быстрые компиляции */
	'catchFE' => false, /* перехват ошибок как исключений, исключений как логов и фатальных ошибок */
	'ErrorStack' => false, /* вывод стека вызовов в сообщении об ошибке в БД */
);

/* Шаблонизатор - имя класса + дефолтовый шаблон */
$config['templater']= array(
	'name'      => 'PlainView',
	'def_tpl'   => 'main.html',
);

$config['templater']= array(
	'name'      => 'Proxy',
	'def_tpl'   => 'main.html',
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