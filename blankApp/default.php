<?php

/* настройки хоста - установка Content-Type: text/html; charset=encoding */
$config['host']=array(
	'encoding' => 'utf-8',
	'lang' => 'ru_RU',
);

/* Настройки коннекта к базе данных */
$config['database']=array(
	'type'     => 'mysql',
	'host'     => 'localhost',
	'username' => 'root',
	'password' => '',
	'dbname'   => '',
	'prefix'   => '',
	'encoding' => 'utf8',
);

/* Настройки перенаправления */
/*
$config['redirection']=array();
$config['redirection']['baseUrl']='/';
$config['redirection']['useIndex']=false;
$config['redirection']['defExt']='';
$config['redirection']['useRewrite']=true;
$config['redirection']['useModuleRewrite']=false;
*/

/* Настройки кешера (класс бекенда и дополнительные параметры, если есть) */
/*$config['cacher']=array(
	'module' => 'Memcache',
);*/
$config['cacher']=array(
	'module' => 'File',
);

/* статус проекта на данном хосте - отладка и всякие быстрые компиляции */
$config['release']= true;

/* Шаблонизатор - имя класса + дефолтовый шаблон */
$config['templater']= array(
	'name'      => 'PlainView',
	'def_tpl'   => 'main.html',
);

/**/
$config['templater']= array(
	'name'      => 'Smarty',
	'def_tpl'   => 'main.tpl',
);
/**/

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
/**/

?>