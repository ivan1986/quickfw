<?php

require_once ('PHPUnit/Framework/TestSuite.php');

class FrameworkTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite();
		
		$base = dirname(__FILE__);
		
		$suite->addTestFile($base.'/QFWTest.php');
		
		return $suite;
	}
	
}

?>