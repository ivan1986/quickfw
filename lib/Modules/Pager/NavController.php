<?php

class NavController
{

	public function pagerBlock($url='$',$all=0,$cur=1,$size=5)
	{
		if ($all<2) return '';
		QFW::$view->assign('pager',array(
			'all'=>$all,
			'c'=>$cur,
			'url'=>$url,
			'size'=>$size,
		));
		return QFW::$view->fetch('pager.html');
	}

}

?>
