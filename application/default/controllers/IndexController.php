<?php

require_once(QFWPATH.'/QuickFW/Auth.php');

class IndexController extends QuickFW_Auth
{
	public function indexAction()
	{
		$this->session();
		$_SESSION['1']++;
		//die();
//		require(APPPATH.'/default/code_template.php');
//		return $table;
		//echo '<pre>'.QFW::$view->P->siteUrl('test').'</pre>';
		$c=getCache('File');
		//$c->setDirectives(array());
		/*$c->save('qwerty','1');
		echo $c->load('1');*/
		//$c->remove('1');
		//print_r(thru($c,QFW::$db,'data')->select('select * from users where id=1'));
		/*echo '<pre>';
		print_r(QFW::$db->select('select * from hb_chat_rooms WHERE afftar_id IN (?a) OR afftar_id=?',array('123','3'),'0'));
		$k=array(
			'afftar_id'=>0,
			'name'=>'111'
		);
		echo QFW::$db->query('update hb_chat_rooms set name=? where id>?','112121',20451);
		echo '</pre>';*/
		//session_destroy();
		return QFW::$view->fetch('b.html');

		return 'Корневое действие сайта, показывается на /, на /default, на /index и т.п.<br/>';
	}

	public function testBlock()
	{
		echo 2;
	}

	public function преведAction()
	{
		QFW::$view->mainTemplate='';
		return "медвед";
	}

	/*public function CacheInfo($action,$params)
	{
		return array(
			'Cacher' => getCache(),
			'id' => 'ALL'.$action,
			//'full'=>1,
		);
	}*/

}

?>