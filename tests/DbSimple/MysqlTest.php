<?php

define('DSNMY','mysql://root@localhost/DbSimple');

require_once 'GenericTestClass.php';

class MysqlTest extends GenericDbSimpleTestClass
{
	protected function setUp()
	{
		$this->db = new QuickFW_AutoDbSimple(DSNMY);
		$this->db->setErrorHandler(false, false);
	}

	protected $Qlog=array();
	public function queryLogger(&$Simpla, $query)
	{
		if (strpos($query,'--')===false)
			$this->Qlog[]=$query;
	}

	public function testMyCellPlaceholder()
	{
		$this->db->setLogger(array(&$this,'queryLogger'));
		$row = array(
			'id'  => 1,
			'str' => 'test'
		);

		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, str VARCHAR(10))");
		$this->db->query("INSERT INTO test(?#) VALUES(?a)", array_keys($row), array_values($row));
		$r = $this->db->selectCol("SELECT ?# FROM test", 'id');

		$R = array (
			0 => '1',
		);
		$Log = array (
			0 => 'DROP TABLE test',
			1 => 'CREATE TABLE test(id INTEGER, str VARCHAR(10))',
			2 => 'INSERT INTO test(`id`, `str`) VALUES(\'1\', \'test\')',
			3 => 'SELECT `id` FROM test',
		);

		$this->assertEquals($r,$R, 'Select Error');
		$this->assertEquals($this->Qlog,$Log);
		$this->Qlog=array();

	}

	public function testMyBlob()
	{
		$this->db->query('DROP TABLE `test`');
		$this->db->query('CREATE TABLE `test` (
			`id` INT NOT NULL ,
			`text` BLOB
			) ENGINE = MYISAM');
		$this->db->query("INSERT INTO test(id, text) VALUES (1,'1234567890')");
		$d = $this->db->selectRow(' -- BLOB_OBJ: true
		select * FROM test');
		$this->assertEquals($d['text']->read(3),'123');
		$this->assertEquals($d['text']->read(3),'456');
		$this->assertEquals($d['text']->read(3),'789');
		$this->assertEquals($d['text']->read(3),'0');
		$this->assertEquals($d['text']->read(3),'');
		$this->db->query('DROP TABLE `test`');
	}

}

?>