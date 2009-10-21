<?php

require (QFWPATH.'/QuickFW/Auth.php');

class Controller extends QuickFW_Auth
{
	function __construct()
	{
		if(!parent::__construct('admin'))
			die (QFW::$view->displayMain(QFW::$view->fetch(('auth.html'))));
	}

	protected function acl($level)
	{
		if ($this->userdata['type'] == 'root')
			return true;
		if ($this->userdata['type'] == 'admin' && $level!='root')
			return true;
		if ($this->userdata['type'] == 'huru' && $level=='huru')
			return true;
		return false;
	}

	protected function checkUser()
	{
		if (!isset($_POST['login']))
			return false;
		$login = $_POST['login'];
		$passw = (isset($_POST['passw']) ? $_POST['passw'] : '');
		foreach (QFW::$config['admin'] as $type=>$users)
			foreach ($users as $log=>$pass)
				if ($log==$login && $pass==$passw)
				{
					$ret = array('type' => $type);
					if (!isset($_POST['no_redirect'])) $ret['redirect'] = QFW::$view->P->siteUrl('admin');
					return $ret;
				}
		return false;
	}

}

?>
