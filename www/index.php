<?php
	define ('DOC_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));
	define ('ROOTPATH', realpath($_SERVER['DOCUMENT_ROOT'] . '/../'));
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