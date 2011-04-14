<?php
	define ('DOC_ROOT', dirname(__FILE__));
	define ('ROOTPATH', dirname(dirname(__FILE__)));
	define ('APPPATH', ROOTPATH . '/application');
	define ('VARPATH', ROOTPATH . '/var');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');

//Настройки по умолчанию, если нужно - измените
//	define ('TMPPATH', VARPATH . '/tmp');
//	define ('LOGPATH', VARPATH . '/log');
//	define ('COMPATH', APPPATH . '/_common');
//	define ('MODPATH', COMPATH . '/models');

	require (QFWPATH.'/Init.php');
	
	QFW::$router->route();

?>