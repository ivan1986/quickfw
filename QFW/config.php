<?php

$config=array();

$config['host']=array(
	'encoding' => 'utf-8',
	'lang' => '',
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

$config['redirection']=array(
	'baseUrl'          => '/',
	'useIndex'         => false,
	'defExt'           => '',
	'useRewrite'       => true,
	'useModuleRewrite' => false,
);

$config['release']=false;

$config['cacher']=array(
	'module' => 'File',
);

$config['templater']= array(
	'name'       => 'PlainView',
	'def_tpl'    => 'main.html',
);

?>