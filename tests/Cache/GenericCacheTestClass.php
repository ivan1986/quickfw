<?php

class GenericCacheTestClass extends PHPUnit_Framework_TestCase
{
	protected $cache;
	protected function setUp()
	{
		$this->cache = getCache();
	}
	
	/**
	 * Простейшая провера кешера - 
	 * что положили - то и должны достать
	 * 
	 * @dataProvider testCacheProvider
	 */
	public function testCache($id,$data)
	{
		$this->cache->save($data,$id);
		$dat = $this->cache->load($id);
		$this->assertEquals($data,$dat);
	}
	
	/**
	 * Соберем строчку из всех возможных символов
	 *
	 * @return array
	 */
	public function testCacheProvider()
	{
		$r = array();
		$a = '';
		for ($c=0;$c<255;$c++)
			$a.= chr($c);
		return array(
			array($a, $a),
			array($a, '1'),
			array('1', $a),
			array('1', '1'),
		);
	}
}

?>