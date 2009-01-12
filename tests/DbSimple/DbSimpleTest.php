<?php

class DbSimpleTest extends PHPUnit_Framework_TestCase
{
	protected $db;

	protected function setUp()
	{
		$this->db = new QuickFW_AutoDbSimple(DSN);
		$this->db->setErrorHandler(false);
	}

	protected function tearDown()
	{
	}

	public function testSetNames()
	{
		$r = $this->db->query('SET NAMES utf8');
		$this->assertEquals($r, 0, 'Ошибка при выполнении запроса SET NAMES utf8');
	}

	public function testCreate()
	{
		$this->db->query('DROP TABLE IF EXISTS `test`');
		$r = $this->db->query('CREATE TABLE `test` (
			`id` INT NOT NULL ,
			`text` VARCHAR( 20 ) NOT NULL ,
			`ts` TIMESTAMP NOT NULL
			) ENGINE = MYISAM');
		$this->assertEquals($r, 0, 'Ошибка при выполнении запроса CREATE TABLE `test`');
		$this->db->query('DROP TABLE `test`');
	}

	public function testReturnEmpty()
	{
		$this->db->query('DROP TABLE IF EXISTS `test`');
		$this->db->query('CREATE TABLE `test` (
			`id` INT NOT NULL ,
			`text` VARCHAR( 20 ) NOT NULL ,
			`ts` TIMESTAMP NOT NULL
			) ENGINE = MYISAM');
		$r = $this->db->select('SELECT * FROM `test`');
		$this->assertEquals($r, array(), 'Возврат пустого select');
		$r = $this->db->selectCol('SELECT * FROM `test`');
		$this->assertEquals($r, array(), 'Возврат пустого selectCol');
		$r = $this->db->selectRow('SELECT * FROM `test`');
		$this->assertEquals($r, array(), 'Возврат пустого selectRow');
		$r = $this->db->selectPage($c,'SELECT * FROM `test`');
		$this->assertEquals($r, array(), 'Возврат пустого selectPage');
		$r = $this->db->selectCell('SELECT * FROM `test`');
		$this->assertEquals($r, null, 'Возврат пустого selectCell');
		$this->db->query('DROP TABLE `test`');
	}

	private $msg;
	private $info;
	public function ErrHandler($msg, $info)
	{
		$this->msg = $msg;
		$this->info = $info;
	}
	
	public function testReturnError()
	{
		$this->db->setErrorHandler(array(&$this,'ErrHandler'));
		$r = $this->db->select('SELECT * FROM `non_exist_table`');
		$msg = 'Table \'DbSimple.non_exist_table\' doesn\'t exist at '.__FILE__.' line '.(__LINE__-1);
		$info = array (
			'code' => 1146,
			'message' => 'Table \'DbSimple.non_exist_table\' doesn\'t exist',
			'query' => 'SELECT * FROM `non_exist_table`',
			'context' => __FILE__.' line '.(__LINE__-6),
		);
		$this->assertEquals($msg, $this->msg, 'Неверное собщение об ошибке');
		$this->assertEquals($info, $this->info, 'Неверный контекст ошибки');
		$this->assertEquals($r, false, 'Возврат ошибки');
	}

	protected $Qlog=array();
	public function queryLogger(&$Simpla, $query)
	{
		if (strpos($query,'--')===false)
			$this->Qlog[]=$query;
	}

	public function testIdentPH()
	{
		$this->Qlog=array();
		$this->db->setLogger(array(&$this,'queryLogger'));
		$this->db->setIdentPrefix('pre_');
		$this->db->query('SELECT ?# FROM ?_t1', 'aaa');
		$this->db->query('SELECT ?# FROM ?_t1', array('aaa'));
		$this->db->query('SELECT ?# FROM ?_t1', array('aaa','bbb'));
		$this->db->query('SELECT ?# FROM ?_t1', array('t1'=>'aaa'));
		$this->db->query('SELECT ?# FROM ?_t1', array('t1'=>array('aaa','bbb')));
		$this->db->query('SELECT ?# FROM ?_t1', array('?_t1'=>'*','?_t2'=>'ccc'));
		$this->db->query('SELECT ?# FROM ?#', array('t1'=>'*','t2'=>'ccc'),array('base'=>'t1'));
		$this->db->query('SELECT ?# FROM ?#', array('?_t1'=>'*','?_t2'=>'ccc'),array('base'=>'t1'));
		$R = array (
			'SELECT `aaa` FROM pre_t1',
			'SELECT `aaa` FROM pre_t1',
			'SELECT `aaa`, `bbb` FROM pre_t1',
			'SELECT `t1`.`aaa` FROM pre_t1',
			'SELECT `t1`.`aaa`, `t1`.`bbb` FROM pre_t1',
			'SELECT `pre_t1`.*, `pre_t2`.`ccc` FROM pre_t1',
			'SELECT `t1`.*, `t2`.`ccc` FROM `base`.`t1`',
			'SELECT `pre_t1`.*, `pre_t2`.`ccc` FROM `base`.`t1`',
		);
		$this->assertEquals($this->Qlog, $R, 'Ошибка обработки ?#');

		$this->Qlog=array();
	}

	public function testArrayPH()
	{
		$this->Qlog=array();
		$this->db->setLogger(array(&$this,'queryLogger'));
		$this->db->setIdentPrefix('pre_');

		$this->db->query('SELECT * FROM t1 WHERE 1=1{ AND a IN (?a)}',array());
		$this->db->query('SELECT * FROM ?_t1 WHERE a IN (?a)', array('1','2','3'));
		$this->db->query('UPDATE ?_t1 SET ?a WHERE a IN (?a)', array('a'=>'1'), array('1','2','3'));
		$this->db->query('UPDATE ?_t1 SET ?a WHERE a IN (?a)', array('a'=>'1', 'b'=>2), array('1','2','3'));
		$this->db->query('UPDATE ?_t1 SET ?a WHERE a IN (?a)', array(
			't' => array('a' => 1, 'b' => 2),
			't2' => array('a' => 3)
		), array('1','2','3'));
		$R = array (
			'SELECT * FROM t1 WHERE 1=1',
			'SELECT * FROM pre_t1 WHERE a IN (\'1\', \'2\', \'3\')',
			'UPDATE pre_t1 SET `a`=\'1\' WHERE a IN (\'1\', \'2\', \'3\')',
			'UPDATE pre_t1 SET `a`=\'1\', `b`=\'2\' WHERE a IN (\'1\', \'2\', \'3\')',
			'UPDATE pre_t1 SET `t`.`a`=\'1\', `t`.`b`=\'2\', `t2`.`a`=\'3\' WHERE a IN (\'1\', \'2\', \'3\')',
		);

		$this->assertEquals($this->Qlog, $R, 'Ошибка обработки ?a');

		$this->Qlog=array();
	}

	public function testSubquery()
	{
		$this->db->setLogger(array(&$this,'queryLogger'));
		$q=$this->db->subquery('SELECT * FROM ?_t1 WHERE a=?','1');
		$this->db->query('?s AND b=?',$q,1);
		$this->db->query('SELECT * FROM t1 WHERE a IN (?a)',array($this->db->subquery('MD5(?)',1)));
		$this->db->query('SELECT ?# FROM t1',array($this->db->subquery('sum(?#)',array('t1'=>'f1')) ) );
		$R = array (
			'SELECT * FROM t1 WHERE a=\'1\' AND b=\'1\'',
			'SELECT * FROM t1 WHERE a IN (MD5(\'1\'))',
			'SELECT sum(`t1`.`f1`) FROM t1',
		);
		$this->assertEquals($this->Qlog,$R);
		$this->Qlog=array();
	}
	
	public function testSkip()
	{
		$this->db->setLogger(array(&$this,'queryLogger'));
		$this->db->query('SELECT * FROM t1 {?WHERE a=1}',1);
		$this->db->query('SELECT * FROM t1 {?WHERE a=1}',DBSIMPLE_SKIP);
		$R = array (
			'SELECT * FROM t1   WHERE a=1 ',
			'SELECT * FROM t1 ',
		);
		$this->assertEquals($this->Qlog,$R);
		$this->Qlog=array();
	}
	
}

?>