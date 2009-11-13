<?php

require ('Controller.php');

class InfoController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->acl('root'))
			QFW::$router->redirectMCA('admin');
	}

	public function memcacheAction()
	{
		$mc = Cache::get('default');
		if(isset($_REQUEST['clearCache']))
			$mc->clean();

		QFW::$view->assign('stat', '<pre>'.print_r($mc->getStats(), true).'</pre>');
		unset($mc);

		return QFW::$view->fetch('memcache.html');
	}

	public function phpinfoAction()
	{
		echo QFW::$view->displayMain('');
		phpinfo();
		die('');
	}

}


?>
