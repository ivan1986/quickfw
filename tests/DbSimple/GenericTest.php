<?php

class GenericTest extends PHPUnit_Framework_TestCase
{
	protected $db;
	protected function setUp()
	{
		$this->db = new QuickFW_AutoDbSimple(DSN);
	}

	public function testArrayKey()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, pid INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(100, 10, 'a')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(101, 10, 'b')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(200, 20, 'x')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(201, 20, 'y')");

		$r = $this->db->select("SELECT id AS ARRAY_KEY, str FROM test ORDER BY id");
		$R = array (
			100 => array (
				'str' => 'a',
			),
			101 => array (
				'str' => 'b',
			),
			200 => array (
				'str' => 'x',
			),
			201 => array (
				'str' => 'y',
			),
		);
		$this->assertEquals($r,$R, 'SELECT id AS ARRAY_KEY, str FROM test ORDER BY id');

		$r = $this->db->select("SELECT id AS ARRAY_KEY_2, pid AS ARRAY_KEY_1, str FROM test ORDER BY id");
		$R = array (
			10 => array (
				100 => array (
					'str' => 'a',
				),
				101 => array (
					'str' => 'b',
				),
			),
			20 => array (
				200 => array (
					'str' => 'x',
				),
				201 => array (
					'str' => 'y',
				),
			),
		);
		$this->assertEquals($r,$R, 'SELECT id AS ARRAY_KEY_2, pid AS ARRAY_KEY_1, str FROM test ORDER BY id');

		$r = $this->db->select("SELECT NULL AS ARRAY_KEY_2, pid AS ARRAY_KEY_1, str FROM test ORDER BY id");
		$R = array (
			10 => array (
				0 => array (
					'str' => 'a',
				),
				1 => array (
					'str' => 'b',
				),
			),
			20 => array (
				0 => array (
					'str' => 'x',
				),
				1 => array (
					'str' => 'y',
				),
			),
		);
		$this->assertEquals($r,$R, 'SELECT NULL AS ARRAY_KEY_2, pid AS ARRAY_KEY_1, str FROM test ORDER BY id');

		$this->db->query("DROP TABLE test");
	}

	public function testParentKey()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, pid INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 1, NULL, 'a')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 2, 1,    'b')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 3, 1,    'c')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 4, 1,    'd')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 5, 2,    'e')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 6, 2,    'f')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 7, 2,    'g')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 8, 3,    'h')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 9, 3,    'i')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(10, 3,    'j')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(11, 4,    'k')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(12, 4,    'l')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(13, 4,    'm')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(14, 5,    'n')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(15, 5,    'o')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(16, 5,    'p')");
		$r = $this->db->select("SELECT id AS ARRAY_KEY, pid AS PARENT_KEY, str FROM test");
		$R = array (
			1 =>
			array (
				'str' => 'a',
				'childNodes' =>
				array (
					2 =>
					array (
						'str' => 'b',
						'childNodes' =>
						array (
							5 =>
							array (
								'str' => 'e',
								'childNodes' =>
								array (
									14 =>
									array (
										'str' => 'n',
										'childNodes' =>
										array (
										),
									),
									15 =>
									array (
										'str' => 'o',
										'childNodes' =>
										array (
										),
									),
									16 =>
									array (
										'str' => 'p',
										'childNodes' =>
										array (
										),
									),
								),
							),
							6 =>
							array (
								'str' => 'f',
								'childNodes' =>
								array (
								),
							),
							7 =>
							array (
								'str' => 'g',
								'childNodes' =>
								array (
								),
							),
						),
					),
					3 =>
					array (
						'str' => 'c',
						'childNodes' =>
						array (
							8 =>
							array (
								'str' => 'h',
								'childNodes' =>
								array (
								),
							),
							9 =>
							array (
								'str' => 'i',
								'childNodes' =>
								array (
								),
							),
							10 =>
							array (
								'str' => 'j',
								'childNodes' =>
								array (
								),
							),
						),
					),
					4 =>
					array (
						'str' => 'd',
						'childNodes' =>
						array (
							11 =>
							array (
								'str' => 'k',
								'childNodes' =>
								array (
								),
							),
							12 =>
							array (
								'str' => 'l',
								'childNodes' =>
								array (
								),
							),
							13 =>
							array (
								'str' => 'm',
								'childNodes' =>
								array (
								),
							),
						),
					),
				),
			),
		);
		$this->assertEquals($r,$R, 'SELECT id AS ARRAY_KEY, pid AS PARENT_KEY, str FROM test');

		$this->db->query("DROP TABLE test");
	}

	public function testSysAliasCi()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, pid INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 1, NULL, 'a')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 2, 1,    'b')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 3, 1,    'c')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 4, 1,    'd')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 5, 2,    'e')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 6, 2,    'f')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 7, 2,    'g')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 8, 3,    'h')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES( 9, 3,    'i')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(10, 3,    'j')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(11, 4,    'k')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(12, 4,    'l')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(13, 4,    'm')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(14, 5,    'n')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(15, 5,    'o')");
		$this->db->query("INSERT INTO test(id, pid, str) VALUES(16, 5,    'p')");
		$r = $this->db->select("SELECT id AS array_key, pid AS Parent_Key, str FROM test");
		$R = array (
		1 =>
		array (
			'str' => 'a',
			'childNodes' =>
			array (
				2 =>
				array (
					'str' => 'b',
					'childNodes' =>
					array (
						5 =>
						array (
							'str' => 'e',
							'childNodes' =>
							array (
								14 =>
								array (
									'str' => 'n',
									'childNodes' =>
									array (
									),
								),
								15 =>
								array (
									'str' => 'o',
									'childNodes' =>
									array (
									),
								),
								16 =>
								array (
									'str' => 'p',
									'childNodes' =>
									array (
									),
								),
							),
						),
						6 =>
						array (
							'str' => 'f',
							'childNodes' =>
							array (
							),
						),
						7 =>
						array (
							'str' => 'g',
							'childNodes' =>
							array (
							),
						),
					),
				),
				3 =>
				array (
					'str' => 'c',
					'childNodes' =>
					array (
						8 =>
						array (
							'str' => 'h',
							'childNodes' =>
							array (
							),
						),
						9 =>
						array (
							'str' => 'i',
							'childNodes' =>
							array (
							),
						),
						10 =>
						array (
							'str' => 'j',
							'childNodes' =>
							array (
							),
						),
					),
				),
				4 =>
				array (
					'str' => 'd',
					'childNodes' =>
					array (
						11 =>
						array (
							'str' => 'k',
							'childNodes' =>
							array (
							),
						),
						12 =>
						array (
							'str' => 'l',
							'childNodes' =>
							array (
							),
						),
						13 =>
						array (
							'str' => 'm',
							'childNodes' =>
							array (
							),
						),
					),
				),
			),
		),
	);
		$this->assertEquals($r,$R, 'SELECT id AS array_key, pid AS Parent_Key, str FROM test');

		$this->db->query("DROP TABLE test");
	}

	public function testSelectCell()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test(id, str) VALUES( 1, 'a')");

		$this->assertEquals($this->db->selectCell("SELECT id FROM test"),'1', 'SELECT id FROM test');
		$this->assertEquals($this->db->selectCell("SELECT str FROM test"),'a', 'SELECT str FROM test');
		$this->assertEquals($this->db->selectCell("SELECT id, str FROM test"),'1', 'SELECT id, str FROM test');

		$this->db->query("DROP TABLE test");
	}

	public function testSelectColMulti()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id1 INTEGER, id2 INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test VALUES( 1, 10, 'a')");
		$this->db->query("INSERT INTO test VALUES( 2, 20, 'b')");
		$this->db->query("INSERT INTO test VALUES( 2, 30, 'c')");
		$this->db->query("INSERT INTO test VALUES( 4, 40, 'd')");
		$r = $this->db->selectCol("SELECT id1 AS ARRAY_KEY_1, str FROM test");
		$R = array (
			1 => 'a',
			2 => 'c',
			4 => 'd',
		);
		$this->assertEquals($r,$R, 'SELECT id1 AS ARRAY_KEY_1, str FROM test');
		$r = $this->db->selectCol("SELECT id1 AS ARRAY_KEY_1, id2 AS ARRAY_KEY_2, str FROM test");
		$R = array (
			1 =>
			array (
				10 => 'a',
			),
			2 =>
			array (
				20 => 'b',
				30 => 'c',
			),
			4 =>
			array (
				40 => 'd',
			),
		);
		$this->assertEquals($r,$R, 'SELECT id1 AS ARRAY_KEY_1, id2 AS ARRAY_KEY_2, str FROM test');

		$this->db->query("DROP TABLE test");
	}

	public function testSelectCol()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test(id, str) VALUES( 1, 'a')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 2, 'b')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 3, 'c')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 4, 'd')");
		$r = $this->db->selectCol("SELECT str FROM test");
		$R = array (
			0 => 'a',
			1 => 'b',
			2 => 'c',
			3 => 'd',
		);
		$this->assertEquals($r,$R, 'SELECT str FROM test');
		$r = $this->db->selectCol("SELECT str, id FROM test");
		$R = array (
			0 => 'a',
			1 => 'b',
			2 => 'c',
			3 => 'd',
		);
		$this->assertEquals($r,$R, 'SELECT str, id FROM test');
		$r = $this->db->selectCol("SELECT str, id FROM test WHERE 1=0");
		$R = array(
		);
		$this->assertEquals($r,$R, 'SELECT str, id FROM test WHERE 1=0');

		$this->db->query("DROP TABLE test");
	}

	public function testSelectRow()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test(id, str) VALUES( 1, 'a')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 2, 'b')");
		$r = $this->db->selectRow("SELECT * FROM test");
		$R = array (
			'id' => '1',
			'str' => 'a',
		);
		$this->assertEquals($r,$R, 'SELECT * FROM test');

		$r = $this->db->selectRow("SELECT str, id FROM test");
		$R = array (
			'str' => 'a',
			'id' => '1',
		);
		$this->assertEquals($r,$R, 'SELECT str, id FROM test');
		$this->db->query("DROP TABLE test");
	}

	public function testSelectPage()
	{
		$this->db->setLogger(array(&$this,'queryLogger'));
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, i INTEGER)");
		for($i=0;$i<100;$i++)
			$this->db->query('INSERT INTO test(id, i) VALUES (?, ?)',$i,$i*10);
		$t =0;
		$r = $this->db->selectPage($t,'SELECT * FROM test LIMIT ?d,?d',10,5);
		$R = array (
			0 => array('id' => '10','i' => '100',),
			1 => array('id' => '11','i' => '110',),
			2 => array('id' => '12','i' => '120',),
			3 => array('id' => '13','i' => '130',),
			4 => array('id' => '14','i' => '140',),
		);
		$this->assertEquals($t,100,'Не работает selectPage - неверный count');
		$this->assertEquals($r,$R,'Не работает selectPage - неверная выборка');
		$this->db->query("DROP TABLE test");
	}

	protected $Qlog=array();
	public function queryLogger(&$Simpla, $query)
	{
		if (strpos($query,'--')===false)
			$this->Qlog[]=$query;
	}

	public function testCache()
	{
		$Cache = new TstCacher;
		$this->db->setCacher($Cache);
		$query = "-- CACHE: 10
			SELECT * FROM test";
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, str VARCHAR(1))");
		$R = array (
			'id' => '1',
			'str' => 'a',
		);
		$this->db->query("INSERT INTO test(id, str) VALUES( 1, 'a')");
		$r = $this->db->selectRow($query);
		$this->assertEquals($r,$R, 'Error before cache');
		$this->db->query("UPDATE test SET str='b' WHERE id=1");
		$r = $this->db->selectRow($query);
		$this->assertEquals($r,$R, 'Error after cache');
		sleep(11);
		$Cache->sleep(20);
		$r = $this->db->selectRow($query);
		$R = array (
			'id' => '1',
			'str' => 'b',
		);
		$this->assertEquals($r,$R, 'Error after timeout of cache');
		$this->db->query("DROP TABLE test");
	}

	public function testNestedBlocksSql()
	{
		$this->db->setLogger(array(&$this,'queryLogger'));
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d } AND c = ?d", 1, 3);
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d } AND c = ?d", DBSIMPLE_SKIP, 3);
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d { AND b=?d } } AND c = ?d", 1, 2, 3);
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d { AND b=?d } } AND c = ?d", 1, DBSIMPLE_SKIP, 3);
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d { AND b=?d } } AND c = ?d", DBSIMPLE_SKIP, 2, 3);
		$R = array (
			0 => 'SELECT * FROM t1 WHERE 1   AND a = 1   AND c = 3',
			1 => 'SELECT * FROM t1 WHERE 1  AND c = 3',
			2 => 'SELECT * FROM t1 WHERE 1   AND a = 1   AND b=2     AND c = 3',
			3 => 'SELECT * FROM t1 WHERE 1   AND a = 1    AND c = 3',
			4 => 'SELECT * FROM t1 WHERE 1  AND c = 3',
		);
		$this->assertEquals($this->Qlog,$R);
		$this->Qlog=array();
	}

	public function testNestedElseBlocksSql()
	{
		$this->db->setLogger(array(&$this,'queryLogger'));
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d | AND c = ?d }", 1, 3);
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d | AND c = ?d }", DBSIMPLE_SKIP, 3);
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d | AND c = ?d { AND a = ?d | AND c = ?d }}",
			DBSIMPLE_SKIP, 3, 2, 4);
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d | AND c = ?d { AND a = ?d | AND d = ?d }}",
			DBSIMPLE_SKIP, 3, DBSIMPLE_SKIP, 4);
		$this->db->query("SELECT * FROM t1 WHERE 1 { AND a = ?d | AND c = ?d {{ AND a = ?d } | {AND c = ?d }}}",
			DBSIMPLE_SKIP, 3, DBSIMPLE_SKIP, 4);
		$this->db->query("SELECT * FROM t1 WHERE 1 { { AND a = ?d | AND b = ?d } AND c = ?d | AND d = ?d }",
			DBSIMPLE_SKIP, 3, DBSIMPLE_SKIP, 4);
		$this->db->query("SELECT * FROM t1 WHERE 1 { { AND a = ?d | AND b = ?d } AND c = ?d | AND d = ?d }",
			DBSIMPLE_SKIP, 3, 2, 4);
		$R = array (
			'SELECT * FROM t1 WHERE 1   AND a = 1  ',
			'SELECT * FROM t1 WHERE 1   AND c = 3  ',
			'SELECT * FROM t1 WHERE 1   AND c = 3   AND a = 2   ',
			'SELECT * FROM t1 WHERE 1   AND c = 3   AND d = 4   ',
			'SELECT * FROM t1 WHERE 1   AND c = 3     ',
			'SELECT * FROM t1 WHERE 1   AND d = 4  ',
			'SELECT * FROM t1 WHERE 1     AND b = 3   AND c = 2  ',
		);
		$this->assertEquals($this->Qlog,$R);
		$this->Qlog=array();
	}
	
	public function testNestedBlocksNativePlaceHolder()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test(id, str) VALUES( 1, 'a')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 2, 'b')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 3, 'c')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 4, 'd')");
		
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d } OR id = ?d", 1, 3);
		$R = array ('a','c',);
		$this->assertEquals($r,$R);
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d } OR id = ?d", DBSIMPLE_SKIP, 3);
		$R = array ('c',);
		$this->assertEquals($r,$R);
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d { OR id = ?d }} OR id = ?d", 1, 2, 3);
		$R = array ('a','b','c',);
		$this->assertEquals($r,$R);
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d { OR id = ?d }} OR id = ?d", 1, DBSIMPLE_SKIP, 3);
		$R = array ('a','c',);
		$this->assertEquals($r,$R);
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d { OR id = ?d }} OR id = ?d", DBSIMPLE_SKIP, 2, 3);
		$R = array ('c',);
		$this->assertEquals($r,$R);
		
		$this->db->query("DROP TABLE test");
	}

	public function testNestedElseBlocksNativePlaceHolder()
	{
		$this->db->query("DROP TABLE test");
		$this->db->query("CREATE TABLE test(id INTEGER, str VARCHAR(1))");
		$this->db->query("INSERT INTO test(id, str) VALUES( 1, 'a')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 2, 'b')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 3, 'c')");
		$this->db->query("INSERT INTO test(id, str) VALUES( 4, 'd')");
		
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d | OR id = ?d }", 1, 3);
		$R = array ('a',);
		$this->assertEquals($r,$R,'1');
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d | OR id = ?d }", DBSIMPLE_SKIP, 3);
		$R = array ('c',);
		$this->assertEquals($r,$R,'2');
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d | OR id = ?d { OR id = ?d | OR id = ?d }}",
			DBSIMPLE_SKIP, 2, 3, 4);
		$R = array ('b','c',);
		$this->assertEquals($r,$R,'3');
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d | OR id = ?d { OR id = ?d | OR id = ?d }}",
			DBSIMPLE_SKIP, 3, DBSIMPLE_SKIP, 4);
		$R = array ('c','d',);
		$this->assertEquals($r,$R,'4');
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { OR id = ?d | OR id = ?d {{ OR id = ?d } | {OR id = ?d }}}",
			DBSIMPLE_SKIP, 3, DBSIMPLE_SKIP, 4);
		$R = array ('c',);
		$this->assertEquals($r,$R,'5');
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { { OR id = ?d | OR id = ?d } OR id = ?d | OR id = ?d }",
			DBSIMPLE_SKIP, 3, DBSIMPLE_SKIP, 4);
		$R = array ('d',);
		$this->assertEquals($r,$R,'6');
		$r = $this->db->selectCol("SELECT str FROM test WHERE 0 { { OR id = ?d | OR id = ?d } OR id = ?d | OR id = ?d }",
			DBSIMPLE_SKIP, 3, 2, 4);
		$R = array ('b','c',);
		$this->assertEquals($r,$R,'7');
		$this->db->query("DROP TABLE test");
	}
	
	public function testIndexPlaceHolder()
	{
		$this->db->setLogger(array(&$this,'queryLogger'));

		$this->db->query("SELECT ?# FROM t1", array('a', 'b'));
		$this->db->query("SELECT ?# FROM t1", array('t1' => 'a', 'b'));

		$R = array (
			0 => 'SELECT `a`, `b` FROM t1',
			1 => 'SELECT `t1`.`a`, `b` FROM t1',
		);

		$this->assertEquals($this->Qlog,$R);
		$this->Qlog=array();
	}

}

?>