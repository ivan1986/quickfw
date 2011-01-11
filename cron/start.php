<?php
	define ('DOC_ROOT', dirname(dirname(__FILE__)).'/www');
	define ('ROOTPATH', dirname(dirname(__FILE__)));
	define ('APPPATH', ROOTPATH . '/application');
	define ('VARPATH', ROOTPATH . '/var');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');

//Настройки по умолчанию, если нужно - измените
//	define ('TMPPATH', VARPATH  . '/tmp');
//	define ('LOGPATH', VARPATH  . '/log');
//	define ('MODPATH', APPPATH  . '/_common/models');

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
