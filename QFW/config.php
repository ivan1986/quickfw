<?php

$config=array();

$config['host']=array(
	'encoding' => 'utf-8',
	'lang' => '',
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
	'delDef'           => true,
);

/**
 * Флаги, влияющие на поведение всяких вещей
 */
$config['QFW'] = array(
	'release' => false, /* статус проекта на данном хосте - отладка и всякие быстрые компиляции */
	'cacheSessions' => false, /* Хранить сессии в кеше, не использовать стандартный механизм */
	'autoload' => true, /* включить автолоад false|true|string */
	'auto404' => false, /* не перенаправлять на дефолтовый контроллер все запросы  */
	'main404' => false, /* 404 страница в дизайне main */
	'addCSSXml' => false, /* addJS и addCSS выводят теги в XML  */
);

/**
 * Настройки обработчика ошибок
 */
$config['error'] = array();
/*$config['error'][] = array(
	'name' => 'mail',
	'RemoveDups' => 300, //секунд или false
	'options' => array(
		'to' => 'user@localhost',
		'whatToSend' => 65535, // LOG_ALL (look in TextNotifier)
		'subjPrefix' => '[ERROR] ',
		'charset' => 'UTF-8',
	),
);*/

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
	/*'MCA' => array(
		'module' => 'Xcache',
		'namespace' => '',
		'tags' => false,
	),*/
);

$config['templater'] = array(
	'name'    => 'PlainView',
	'def_tpl' => 'main.php',
);

?>