<?php
	define ('DOC_ROOT', realpath(dirname(__FILE__).'../www'));
	define ('ROOTPATH', realpath(dirname(__FILE__).'/../'));
	define ('APPPATH', ROOTPATH . '/application');
	define ('TMPPATH', ROOTPATH . '/tmp');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');
	define ('MODPATH', APPPATH . '/_common/models');

	require (QFWPATH.'/Init.php');

	array_shift($argv);
	$argv=join('/',$argv);

	QFW::$view->mainTemplate='';
	QFW::$router->route($argv,'Cli');

?>