<?php
	define ('DOC_ROOT', dirname(__FILE__));
	define ('ROOTPATH', dirname(dirname(__FILE__)));
	define ('APPPATH', ROOTPATH . '/application');
	define ('VARPATH', ROOTPATH . '/var');
	define ('TMPPATH', VARPATH  . '/tmp');
	define ('LOGPATH', VARPATH  . '/log');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');
	define ('MODPATH', APPPATH  . '/_common/models');
	
	require (QFWPATH.'/Init.php');
	
	QFW::$router->route();

?>