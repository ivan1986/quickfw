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
		if (isset($_REQUEST[session_name()]))
			$this->session();
		
		$this->name=$name;
		$this->userdata = $this->authorized = false;
		if (isset($_SESSION[$name]))
		{
			$this->authorized = true;
			$this->userdata = & $_SESSION[$name];
			return true;
		}
		$this->checkPostData();
		if ($this->authorized)
			return true;
		if ($redir===false)
			return false;
		QFW::$router->route($redir);
		die();
	}
	
	/**
	 * Старт сессии
	 *
	 * Если сессия уже есть ничего не делает
	 * Если сессии нет, то стартует новая
	 *
	 * @param string $sid - идентификатор сессии
	 */
	function session($sid = '')
	{
		if (QuickFW_Auth::$session!=null)
			return;
		require (QFWPATH.'/QuickFW/Session.php');
		QuickFW_Auth::$session = new QuickFW_Session($sid);
	}
	
	/**
	 * Старт сессии
	 *
	 * Если сессия уже есть - уничтожает старую и стартует новую
	 * Если сессии нет, то стартует новая
	 *
	 * @param string $sid - идентификатор сессии
	 */
	function sessionRestart($sid = '')
	{
		if (QuickFW_Auth::$session!=null)
			return QuickFW_Auth::$session->restart($sid);
		require (QFWPATH.'/QuickFW/Session.php');
		QuickFW_Auth::$session = new QuickFW_Session($sid);
	}
	
	/**
	 * Уничтожение сессии
	 */
	function sessionDestroy()
	{
		if (QuickFW_Auth::$session==null)
			return;
		QuickFW_Auth::$session->destroy(session_id());
	}
	
	/**
	 * Check if we get data from form with username and password
	 *
	 * @return boolean
	 */
	private function checkPostData()
	{
		$data = $this->checkUser();
		if ($data === false)
			return; //неудачный логин
		
		$this->session();
		
		$_SESSION[$this->name] = $data;
		if (is_array($data) && array_key_exists('redirect',$data))
		{
			$r=$_SESSION[$this->name]['redirect'];
			unset($_SESSION[$this->name]['redirect']);
			QFW::$router->redirect($r===true?$_SERVER['REQUEST_URI']:$r);
		}
		$this->authorized = true;
		$this->userdata = & $_SESSION[$this->name];
	}
	
	//You can overload this!
	protected function checkUser()
	{
		if (!isset($_POST[self::USERNAME_FIELD]))
			return false;
		global $config;
		$username = isset($_POST[self::USERNAME_FIELD]) ? $_POST[self::USERNAME_FIELD] : null;
		$password = isset($_POST[self::PASSWORD_FIELD]) ? $_POST[self::PASSWORD_FIELD] : null;
		//Check if this admin
		if
		(
			(strcasecmp($config['admin']['login'],    trim($username)) == 0)
		and
			(strcasecmp($config['admin']['password'], trim($password)) == 0)
		)
			return $username;
		else
			return false;
	}
	
}
?>