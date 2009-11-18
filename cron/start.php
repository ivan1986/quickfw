<?php
	define ('DOC_ROOT', realpath(dirname(__FILE__).'../www'));
	define ('ROOTPATH', realpath(dirname(__FILE__).'/../'));
	define ('APPPATH', ROOTPATH . '/application');
	define ('TMPPATH', ROOTPATH . '/tmp');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');
	define ('MODPATH', APPPATH . '/_common/models');

	//TODO: Выкинуть gethostbyaddr после перехода на PHP 5.3
	if (empty($_SERVER['HTTP_HOST']))
		$_SERVER['HTTP_HOST'] = function_exists('gethostname') ?
			gethostname() : gethostbyaddr('127.0.0.1');

	require (QFWPATH.'/Init.php');

	array_shift($argv);
	$argv=join('/',$argv);

	QFW::$view->mainTemplate='';
	QFW::$router->route($argv,'Cli');

?>
