<?php

class IndexController
{
	public function indexAction()
	{
		echo '<pre>'.QFW::$view->P->siteUrl('test').'</pre>';
		$c=getCache('File',true);
		$c->save('data','id',array('1','2','3'));
		echo $c->load('id');
		//$c->clean(CACHE_CLR_TAG,array('1'));
		echo $c->load('id');
		return 'Корневое действие сайта, показывается на /, на /default, на /index и т.п.<br/>';
	}
	
	public function преведAction()
	{
		QFW::$view->mainTemplate='';
		return "медвед";
	}

	public function getTimestamp($action,$params)
	{
		return mktime();
	}
	
}

?>