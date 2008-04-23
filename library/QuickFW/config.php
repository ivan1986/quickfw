<?php

$config=array();

$config['host']=array(
	'encoding' => 'utf-8',
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

$config['state']=array(
	'release'     => true,
	'autosession' => true,
	'def_tpl'     => 'main.tpl',
);

$config['cacher']=array(
	'module' => 'File',
);

$config['templater']= array(
	'name'=>'Smarty',
	'debug'=>false,
);

?>