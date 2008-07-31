<?php
	define ('DOC_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));
	define ('ROOTPATH', realpath($_SERVER['DOCUMENT_ROOT'] . '/../'));
	define ('APPPATH', ROOTPATH . '/application');
	define ('TMPPATH', ROOTPATH . '/tmp');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');

	require (QFWPATH.'/Init.php');

	QFW::$router->route();
?>