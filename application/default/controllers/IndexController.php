<?php

class IndexController
{
	public function indexAction()
	{
		echo '<pre>'.QFW::$view->P->siteUrl('test').'</pre>';
		
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