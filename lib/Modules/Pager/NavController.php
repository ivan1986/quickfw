<?php

class NavController
{

	public function pagerBlock($url='$',$all=0,$cur=1)
	{
		if ($all<2) return "";
		QFW::$view->assign('pager',array(
			'all'=>$all,
			'c'=>$cur,
			'url'=>$url,
		));
		return QFW::$view->fetch('pager.html');
	}
	
}

?>