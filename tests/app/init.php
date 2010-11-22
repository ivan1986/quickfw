<?php
	define ('DOC_ROOT', dirname(__FILE__));
	define ('ROOTPATH', dirname(dirname(dirname(__FILE__))));
	define ('APPPATH', dirname(dirname(__FILE__)).'/testapp');
	define ('VARPATH', ROOTPATH . '/var');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');

	$_SERVER['HTTP_HOST'] = 'test';

	require (QFWPATH.'/Init.php');
	

?>