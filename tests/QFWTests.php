<?php
	define ('ROOTPATH', dirname(dirname(__FILE__)));
	define ('DOC_ROOT', ROOTPATH . '/www');
	define ('APPPATH',  ROOTPATH . '/tests/testapp');
	define ('TMPPATH',  ROOTPATH . '/tmp');
	define ('QFWPATH',  ROOTPATH . '/QFW');
	define ('LIBPATH',  ROOTPATH . '/lib');
	define ('MODPATH',  APPPATH  . '/_common/models');

	$_SERVER['HTTP_HOST'] = 'test';
	
	require (QFWPATH.'/Init.php');

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