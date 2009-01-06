<?php

require_once 'GenericCacheTestClass.php';

class FileCacheTest extends GenericCacheTestClass
{
	protected function setUp()
	{
		$this->cache = getCache('File');
	}
	
}

class XCacheTest extends GenericCacheTestClass
{
	protected function setUp()
	{
		if (!extension_loaded('xcache'))
			$this->markTestSkipped('Xcache not loaded');
		$this->cache = getCache('Xcache');
	}
	
}

class MemcacheTest extends GenericCacheTestClass
{
	protected function setUp()
	{
		if (!extension_loaded('memcache'))
			$this->markTestSkipped('Memcache not loaded');
		$this->cache = getCache('Memcache');
	}
	
}

class BdbCacheTest extends GenericCacheTestClass
{
	protected function setUp()
	{
		if (!extension_loaded('dba'))
			$this->markTestSkipped('DBA not loaded');
		$this->cache = getCache('Bdb');
	}
	
}

?>