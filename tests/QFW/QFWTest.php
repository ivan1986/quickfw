<?php

class QFWTest extends PHPUnit_Framework_TestCase
{
	public function testInitTime()
	{
		global $InitTime;
		$this->assertLessThanOrEqual(0.05,$InitTime);
		unset($GLOBALS['InitTime']);
	}

	public function testVars()
	{
		/*$this->assertEquals(QFW::$globalData,array());
		$this->assertTrue(QFW::$router instanceof QuickFW_Router);
		QFW::$config;
		$this->assertTrue(QFW::$view instanceof Templater_Proxy);
		QFW::$view;
		QFW::$libs;
		QFW::$db;*/
	}

}

?>