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

	/**
	 * Пока выдаются такие результаты
	 *
	 * @return array
	 */
	public function testDdefProvider()
	{
		return array(
			array('aaa/bbb/ccc', ''),
			array('aaa/bbb/fff', 'bbb/fff'),
			array('aaa/eee/ccc', 'eee'),
			array('aaa/eee/fff', 'eee/fff'),
			array('ddd/bbb/ccc', 'ddd/bbb/ccc'),
			array('ddd/bbb/fff', 'ddd/bbb/fff'),
			array('ddd/eee/ccc', 'ddd/eee/ccc'),
			array('ddd/eee/fff', 'ddd/eee/fff'),
			array('aaa/bbb', 'bbb'),
			array('aaa/eee', 'eee'),
			array('ddd/bbb', 'ddd/bbb'),
			array('ddd/eee', 'ddd/eee'),
			array('aaa/ccc', 'ccc'),
			array('aaa/fff', 'fff'),
			array('ddd/ccc', 'ddd'),
			array('ddd/fff', 'ddd/fff'),
			array('bbb/ccc', ''),
			array('bbb/fff', 'bbb/fff'),
			array('eee/ccc', 'eee'),
			array('eee/fff', 'eee/fff'),
			array('aaa', ''),
			array('ddd', 'ddd'),
			array('bbb', 'bbb'),
			array('eee', 'eee'),
			array('ccc', 'ccc'),
			array('fff', 'fff'),
		);
	}
	
	/**
	 * @dataProvider testDdefProvider
	 *
	 * Тестирование delDef
	 */
	public function testDdef($in,$out)
	{
		QFW::Init();
		QFW::$config['default']['module'] = 'aaa';
		QFW::$config['default']['controller'] = 'bbb';
		QFW::$config['default']['action'] = 'ccc';
		QFW::$router->__construct(APPPATH);
		$this->assertEquals(QFW::$router->delDef($in),$out);
	}
	
}

?>