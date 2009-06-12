<?php
	define ('DOC_ROOT', dirname(__FILE__));
	define ('ROOTPATH', dirname(dirname(__FILE__)));
	define ('APPPATH', ROOTPATH . '/application');
	define ('TMPPATH', ROOTPATH . '/tmp');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');
	define ('MODPATH', APPPATH  . '/_common/models');
	
	$DONE = 0;
	
	require (QFWPATH.'/Init.php');
	
	QFW::$router->route();
	
	$DONE = 1;
?>