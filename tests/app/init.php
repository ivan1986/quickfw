<?php
	define ('DOC_ROOT', dirname(__FILE__));
	define ('ROOTPATH', dirname(dirname(dirname(__FILE__))));
	define ('APPPATH', dirname(dirname(__FILE__)).'/testapp');
	define ('TMPPATH', ROOTPATH . '/tmp');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');
	define ('MODPATH', APPPATH  . '/_common/models');

	$_SERVER['HTTP_HOST'] = 'test';

	require (QFWPATH.'/Init.php');
	

?>