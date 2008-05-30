<?php

$config=array();

$config['host']=array(
	'encoding' => 'utf-8',
	'lang' => 'ru_RU',
);

$config['database']=array(
	'type'     => 'mysql',
	'host'     => 'localhost',
	'username' => 'root',
	'password' => '',
	'dbname'   => 'base',
	'prefix'   => '',
	'encoding' => 'utf8',
);

$config['redirection']=array(
	'baseUrl'          => '/',
	'useIndex'         => false,
	'defExt'           => '',	//или пусто или .html например
	'useRewrite'       => true,
	'useModuleRewrite' => false,
);

$config['release']=true;

$config['cacher']=array(
	'module' => 'File',
);

$config['templater']= array(
	'name'       =>'Smarty',
	'def_tpl'    => 'main.tpl',
);

mb_internal_encoding("UTF-8");

?>