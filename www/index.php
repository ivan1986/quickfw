<?php
	define ('DOC_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));
	define ('ROOTPATH', realpath($_SERVER['DOCUMENT_ROOT'] . '/../'));
	define ('APPPATH', ROOTPATH . '/application');
	define ('TMPPATH', ROOTPATH . '/tmp');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');
	define ('LOGPATH', ROOTPATH . '/log');
	define ('MODPATH', APPPATH  . '/_common/models');

	require (QFWPATH.'/Init.php');

	QFW::$router->route();
?>