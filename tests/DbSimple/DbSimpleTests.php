<?php

require_once 'PHPUnit/Framework.php';
require_once LIBPATH.'/DbSimple/Generic.php';
define('DSN','mysql://root@localhost/DbSimple');

class DbSimpleTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite();
		
		$base = dirname(__FILE__);
		require_once $base.'/lib.php';
		
		$suite->addTestFile($base.'/DbSimpleTest.php');
		$suite->addTestFile($base.'/MysqlTest.php');
		$suite->addTestFile($base.'/MypdoTest.php');
		//$suite->addTestFile($base.'/PgsqlTest.php');
		
		return $suite;
	}
	
}

?>