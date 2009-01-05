<?php

chdir(dirname(__FILE__));

require_once 'PHPUnit/Framework.php';

require_once QFWPATH.'/DbSimple/Generic.php';
require_once 'lib.php';

define('DSN','mysql://root@localhost/DbSimple');

class DbSimpleTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite();
		
		$suite->addTestFile('DbSimpleTest.php');
		$suite->addTestFile('GenericTest.php');
		$suite->addTestFile('MysqlTest.php');
		$suite->addTestFile('MypdoTest.php');
		$suite->addTestFile('PgsqlTest.php');
		
		return $suite;
	}
	
}

?>