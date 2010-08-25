--TEST--
QFW: test delDef
--FILE--
<?php
require dirname(__FILE__).'/init.php';

QFW::Init();
QFW::$config['default']['module'] = 'aaa';
QFW::$config['default']['controller'] = 'bbb';
QFW::$config['default']['action'] = 'ccc';

$test = array(
	'aaa/bbb/ccc' => '',
	'aaa/bbb/fff' => 'bbb/fff',
	'aaa/eee/ccc' => 'eee',
	'aaa/eee/fff' => 'eee/fff',
	'ddd/bbb/ccc' => 'ddd/bbb',
	'ddd/bbb/fff' => 'ddd/bbb/fff',
	'ddd/eee/ccc' => 'ddd/eee',
	'ddd/eee/fff' => 'ddd/eee/fff',
	'aaa/bbb' => '',
	'aaa/eee' => 'eee',
	'ddd/bbb' => 'ddd/bbb',
	'ddd/eee' => 'ddd/eee',
	'aaa/ccc' => '',
	'aaa/fff' => 'fff',
	'ddd/ccc' => 'ddd',
	'ddd/fff' => 'ddd/fff',
	'bbb/ccc' => '',
	'bbb/fff' => 'bbb/fff',
	'eee/ccc' => 'eee',
	'eee/fff' => 'eee/fff',
	'aaa' => '',
	'ddd' => 'ddd',
	'bbb' => '',
	'eee' => 'eee',
	'ccc' => '',
	'fff' => 'fff',
);

QFW::$router->__construct(APPPATH);

foreach($test as $in=>$out)
	if (QFW::$router->delDef($in) != $out)
		echo $in.' != '.$out."\n";


--EXPECT--
