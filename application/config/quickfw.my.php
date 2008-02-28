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

?>