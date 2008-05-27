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
	'def_tpl'     => 'main.html',
);

$config['templater']= array(
	'name'=>'PlainView',
);

/*
$config['state']=array(
	'release'     => true,
	'autosession' => true,
	'def_tpl'     => 'main.tpl',
);

$config['templater']= array(
	'name'=>'Smarty',
);
/**/

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
}/**/

?>