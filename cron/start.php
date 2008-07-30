<?php
	define ('DOC_ROOT', realpath(dirname(__FILE__).'../www'));
	define ('ROOTPATH', realpath(dirname(__FILE__).'/../'));
	define ('APPPATH', ROOTPATH . '/application');
	define ('TMPPATH', ROOTPATH . '/tmp');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');
	
	require (QFWPATH.'/Init.php');

	require (QFWPATH.'/QuickFW/Router.php');
	$router = new QuickFW_Router(APPPATH);

	QFW::Init();
	
	array_shift($argv);
	$argv=join('/',$argv);
	
	$router->route($argv,'Cli');
?>