<?php

$config['database']=array(
	'type'     => 'mysql',
	'host'     => 'localhost',
	'username' => 'root',
	'password' => '',
	'dbname'   => 'amarok',
	'prefix'   => '',
	'encoding' => 'utf8',
);

/*$config['cacher']=array(
	'module' => 'Memcache',
);*/
$config['cacher']=array(
	'module' => 'File',
);

$config['state']=array(
	'release'     => true,
	'autosession' => true,
	'def_tpl'     => 'main.html',
);

$config['templater']= array(
	'name'=>'PlainView',
	'debug'=>false,
);

/**/
$config['state']=array(
	'release'     => true,
	'autosession' => true,
	'def_tpl'     => 'main.tpl',
);

$config['templater']= array(
	'name'=>'Smarty',
	'debug'=>false,
);
/**/

?>