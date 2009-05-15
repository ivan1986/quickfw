<?php

define('DSNPG','postgresql://test:test@localhost/test');

class PgsqlTest extends PHPUnit_Framework_TestCase
{
	protected $pg;
	protected function setUp()
	{
		$this->pg = new QuickFW_AutoDbSimple(DSNPG);
		$this->pg->setErrorHandler(false, false);
	}

	protected $Qlog=array();
	public function queryLogger(&$Simpla, $query)
	{
		if (strpos($query,'--')===false)
			$this->Qlog[]=$query;
	}

	public function testPgCellPlaceholder()
	{
		$this->pg->setLogger(array(&$this,'queryLogger'));
		$row = array(
			'id'  => 1,
			'str' => 'test'
		);

		$this->pg->query("DROP TABLE IF EXISTS testT");
		$this->pg->query("CREATE TABLE testT(id INTEGER, str VARCHAR(10))");
		$this->pg->query("INSERT INTO testT(?#) VALUES(?a)", array_keys($row), array_values($row));
		$R = array (
			0 => '1',
		);
		$Log = array (
			0 => 'DROP TABLE IF EXISTS testT',
			1 => 'CREATE TABLE testT(id INTEGER, str VARCHAR(10))',
			2 => 'INSERT INTO testT("id", "str") VALUES(\'1\', \'test\')',
			3 => 'SELECT "id" FROM testT',
		);
		$r = $this->pg->selectCol("SELECT ?# FROM testT", 'id');
		$this->assertEquals($r,$R, 'CellPlaceholder result');
		$this->assertEquals($this->Qlog,$Log, 'CellPlaceholder Log');
		$this->pg->query("DROP TABLE testT");
		$this->Qlog=array();

	}

	public function testPgLastInsertId()
	{
		$this->pg->query("DROP TABLE IF EXISTS testT");
		$this->pg->query("CREATE TABLE testT(id SERIAL, str VARCHAR(10)) WITH OIDS");
		$r = $this->pg->query("INSERT INTO testT(str) VALUES ('test')");
		$this->assertType('integer',$r, 'Возвращен неверный тип insert_id');

		$r = $this->pg->select("SELECT * FROM testT");
		$R = array (
			0 => array (
				'id' => '1',
				'str' => 'test',
			),
		);
		$this->assertEquals($r,$R, 'SELECT * FROM testT');
		$r = $this->pg->select("SELECT 1 AS a");
		$R = array (
			0 => array (
				'a' => '1',
			),
		);
		$this->assertEquals($r,$R, 'SELECT 1 AS a');
		$this->pg->query("DROP TABLE testT");

	}

	public function testPgInsertRule()
	{
		$this->pg->setLogger(array(&$this,'queryLogger'));
		$this->pg->query("DROP TABLE IF EXISTS testR");
		$this->pg->query("CREATE TABLE testR(id SERIAL, str VARCHAR(10))");
		$this->pg->query("CREATE RULE test_r AS ON INSERT TO testR DO (SELECT 111 AS id)");
		$r = $this->pg->query("INSERT INTO testR(str) VALUES ('test')");
		$R = array (
			0 => array (
				'id' => '111',
			),
		);
		$this->assertEquals($r,$R, 'Rule generated');
		$r = $this->pg->query("SELECT * FROM testR");
		$R = array (
			0 => array (
				'id' => '1',
				'str' => 'test',
			),
		);
		$this->assertEquals($r,$R, 'Table content');
		$Log = array (
			0 => 'DROP TABLE IF EXISTS testR',
			1 => 'CREATE TABLE testR(id SERIAL, str VARCHAR(10))',
			2 => 'CREATE RULE test_r AS ON INSERT TO testR DO (SELECT 111 AS id)',
			3 => 'INSERT INTO testR(str) VALUES (\'test\')',
			4 => 'SELECT * FROM testR',
		);
		$this->assertEquals($this->Qlog,$Log, 'CellPlaceholder Log');

		$this->Qlog=array();
		$this->pg->query("DROP RULE test_r ON testR");
		$this->pg->query("DROP TABLE testR");
	}

}

?>