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

$config['database']=array(
	'type'     => '',
	'host'     => '',
	'username' => '',
	'password' => '',
	'dbname'   => '',
	'prefix'   => '',
	'encoding' => '',
);
$config['database']='mypdo://root@localhost/base?enc=utf8';

$config['redirection']=array(
	'baseUrl'          => '/',
	'useIndex'         => false,
	'defExt'           => '',
	'useRewrite'       => true,
	'useBlockRewrite'  => false,
);

$config['release'] = false;
$config['catchFE'] = false;

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