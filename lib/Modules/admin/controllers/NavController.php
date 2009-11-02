<?php

class NavController
{

	public function pagerBlock($url='$',$all=0,$cur=1)
	{
		if ($all<2) return '';
		QFW::$view->assign('pager',array(
			'all'=>$all,
			'c'=>$cur,
			'url'=>$url,
		));
		return QFW::$view->fetch('pager.html');
	}

	public function menuBlock($type)
	{
		//выбор меню
		$data = array();
		$data['huru'] = array(
		);

		$data['admin'] = array(
		) + $data['huru'];

		$data['root'] = array(
			'memcache' => 'admin/info/memcache',
			'phpinfo()'=> 'admin/info/phpinfo',
		) + $data['admin'];

		$data = $data[$type];

		return QFW::$view->assign('menu',$data)->fetch('sub/head_stripe.html');
	}

}

?>
