<?php

class QuickFW_Auth
{
	const USERNAME_FIELD = 'login';
	const PASSWORD_FIELD = 'password';
	
	static private $session=null;
	protected $authorized;
	protected $userdata;
	protected $name;
	
	function __construct($name='user',$redir=false)
	{
		if (QuickFW_Auth::$session==null)
		{
			require (LIBPATH.'/QuickFW/Session.php');
			QuickFW_Auth::$session = new QuickFW_Session();
		}

		$this->name=$name;
		$this->authorized = false;
		if (isset($_SESSION[$name]))
		{
			$this->authorized = true;
			$this->userdata = & $_SESSION[$name];
			return true;
		}
		$this->checkPostData();
		if (!$this->authorized)
		{
			if ($redir!==false)
			{
				QFW::$router->route($redir);
				die();
			}
			else
				return false;
		}
		return true;
	}
	
	function isAuthorized()
	{
		return $this->authorized;
	}
	
	function getUser()
	{
		if ($this->authorized)
			return $this->userdata;
		return false;
	}
	
	/**
	 * Check if we get data from form with username and password
	 *
	 * @return boolean
	 */
	function checkPostData()
	{
		$data = $this->checkUser();
		if ($data === false)
			return;	//неудачный логин
		$_SESSION[$this->name] = $data;
		if ($data!==null)	//не автосессия - редирект с POST
		{
			QFW::$router->redirect($_SERVER['REQUEST_URI']);
		}
	}
	
	//You can overload this!
	protected function checkUser()
	{
		global $config;
		$username = isset($_POST[self::USERNAME_FIELD]) ? $_POST[self::USERNAME_FIELD] : null;
		$password = isset($_POST[self::PASSWORD_FIELD]) ? $_POST[self::PASSWORD_FIELD] : null;
		//Check if this admin
		if
		(
			(strcasecmp($config['admin']['login'],   trim($username)) == 0)
		and
			(strcasecmp($config['admin']['password'], trim($password)) == 0)
		)
			return $username;
		else
			return false;
	}
	
}
?>