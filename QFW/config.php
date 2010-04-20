<?php

$config=array();

$config['host']=array(
	'encoding' => 'utf-8',
	'lang' => '',
	'logpath' => ROOTPATH.'/log',
);

$config['default']=array(
	'module'     => 'default',
	'controller' => 'index',
	'action'     => 'index',
);

/*
 * Настройки коннекта к базе данных в формате
 * driver:[//[user[:pass]@]host[:port]]base[?enc=codepage][&persist=(0|1)][&timeout=(sec|0)]
 */
$config['database']='mypdo://root@localhost/base?enc=utf8';

$config['redirection']=array(
	'baseUrl'          => '/',
	'useIndex'         => false,
	'defExt'           => '',
	'useRewrite'       => true,
	'useBlockRewrite'  => false,
);

/**
 * Флаги, влияющие на поведение всяких вещей
 */
$config['QFW'] = array(
	'release' => false, /* статус проекта на данном хосте - отладка и всякие быстрые компиляции */
	'catchFE' => false, /* перехват ошибок как исключений, исключений как логов и фатальных ошибок */
	'ErrorStack' => false, /* вывод стека вызовов в сообщении об ошибке в БД */
	'cacheSessions' => false, /* Хранить сессии в кеше, не использовать стандартный механизм */
	'autoload' => false, /* включить автолоад false|true|string */
);

/**
 * С этими параметрами вызывается функция
 * session_set_cookie_params
 * Менять порядок нельзя - отломается, дописывать можно
 */
$config['session'] = array(
	'lifetime' => 3600,
	'path' => '/',
	//'domain' => '.mydomain.ru',
	//'secure' => false,
	//'httponly' => false,
);

$config['cacher']=array(
	'module' => 'File',
);
$config['cache'] = array(
	'default' => array(
		'module' => 'File',
		'namespace' => '',
		'tags' => false,
	),
);

$config['templater'] = array(
	'name'    => 'PlainView',
	'def_tpl' => 'main.html',
);

?>