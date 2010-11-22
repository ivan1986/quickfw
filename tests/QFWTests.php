<?php
	define ('ROOTPATH', dirname(dirname(__FILE__)));
	define ('DOC_ROOT', ROOTPATH . '/www');
	define ('APPPATH',  ROOTPATH . '/tests/testapp');
	define ('VARPATH',  ROOTPATH . '/vap');
	define ('QFWPATH',  ROOTPATH . '/QFW');
	define ('LIBPATH',  ROOTPATH . '/lib');
	define ('MODPATH',  APPPATH  . '/_common/models');

	$_SERVER['HTTP_HOST'] = 'test';

	$InitTime = microtime(true);
	require_once (QFWPATH.'/Init.php');
	$InitTime = microtime(true) - $InitTime;

	chdir(dirname(__FILE__));

	class QFWTests
	{
		public static function suite()
		{
			$suite = new PHPUnit_Framework_TestSuite();

			$suite->addTestFile('DbSimple/DbSimpleTests.php');
			$suite->addTestFile('Cache/CacheTest.php');
			
			return $suite;
		}

	}

?>