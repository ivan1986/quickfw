<?php

class IndexController
{
	public function indexAction()
	{
		echo '<pre>'.QFW::$view->P->siteUrl('test').'</pre>';
		//$c=getCache('File');
		//$c->setDirectives(array());
		//$c->save('qwerty','1');
		//echo $c->load('1');
		//print_r(thru($c,QFW::$db,'data')->select('select * from users where id=1'));
		//print_r(QFW::$db->select('select * from users where id=1'));
		return 'Корневое действие сайта, показывается на /, на /default, на /index и т.п.<br/>';
	}
	
	public function преведAction()
	{
		QFW::$view->mainTemplate='';
		return "медвед";
	}

	public function CacheInfo($action,$params)
	{
		return array(
			'Cacher' => getCache(),
			'id' => 'ALL'.$action,
			//'full'=>1,
		);
	}
	
}

?>