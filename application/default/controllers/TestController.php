<?php

class TestController
{
	public function __construct()
	{
	}

	public function indexBlock()
	{
		return "<pre>".QFW::$view->render('b.html')
		."\nБлок index - ".QFW::$router->UriPath.' '.QFW::$router->CurPath.' '.QFW::$router->ParentPath
		."\nЭто результат работы блока index с параметрами ". print_r(func_get_args(),true)."</pre>";
	}

	public function aBlock()
	{
		return QFW::$view->render('b.html')
		."\nБлок A - ".QFW::$router->UriPath.' '.QFW::$router->CurPath.' '.QFW::$router->ParentPath;
	}

	public function bBlock()
	{
		return QFW::$view->render('b.html')."\nБлок B - ".
			QFW::$router->UriPath.' '.QFW::$router->CurPath.' '.QFW::$router->ParentPath;
	}

	public function aCli()
	{
		QFW::$view->mainTemplate = '';
		require_once LIBPATH.'/Curl.php';
		$C = new Curl(TMPPATH.'/cookie');
		$data = $C->get('http://u1.imperion.org/');
		$pos = mb_strpos($data->body, 'Ivan1986');
		if (!$pos)
		{
			$form = $data->forms(0);
			$f = $form['fields'];
			$f['player[name]'] = 'Ivan1986';
			$f['player[pw]'] = '1qazxsw2';
			$data = $C->post('http://u1.imperion.org'.$form['action'], $f);
			$data = $C->get('http://u1.imperion.org/');
		}

		$planets = array(
			'93919407',
			'93919406',
			'93919402',
			'93999501',
			'93999502',
			'93999507',
			'93999508',
			'93839201',
			'93839207',
			'93839208',
		);
		$planet = array_rand($planets);
		$planet = $planets[$planet];

		$data = $C->get('http://u1.imperion.org/fleetBase/mission/1/planetId/'.$planet);
		$form = $data->forms(0);
		$f = $form['fields'];

		$m = array();
		preg_match('#shipCount_5">\(([0-9]+)\)</a>#', $data, $m);
		if (!empty($m[1]))
		{
			$d = array(
				'f' => $f['f'],
				'mission' => '302',
				'planet' => $planet,
				'ship[5]' => $m[1],
				'tan' => $f['tan'],
				'tank_text' => '',
				'targetType' => 'p',
			);
			$data = $C->post('http://u1.imperion.org'.$form['action'], $d);
		}
	}
	

}

?>