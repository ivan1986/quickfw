<?php
	define ('DOC_ROOT', dirname(dirname(__FILE__)).'/www');
	define ('ROOTPATH', dirname(dirname(__FILE__)));
	define ('APPPATH', ROOTPATH . '/application');
	define ('VARPATH', ROOTPATH . '/var');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', QFWPATH . '/lib');

//Настройки по умолчанию, если нужно - измените
//	define ('TMPPATH', VARPATH . '/tmp');
//	define ('LOGPATH', VARPATH . '/log');
//	define ('COMPATH', APPPATH . '/_common');

	if (empty($_SERVER['HTTP_HOST']))
		$_SERVER['HTTP_HOST'] = gethostname();

	require (QFWPATH.'/Init.php');

	array_shift($argv);
	$argv=join('/',$argv);

	QFW::$view->mainTemplate='';
	QFW::$router->route($argv,'Cli');

?>
