<?php

class QuickFW_Auth
{
	const USERNAME_FIELD = 'username';
	const PASSWORD_FIELD = 'password';
	
	protected $authorized;
	protected $username;
	
	
	function __construct()
	{
		//session_start();
		$this->authorized = false;
		if (isset($_SESSION['authorized']) AND ($_SESSION['authorized'] === true))
		{
			if (!isset($_SESSION['username']))
			{
				unset($_SESSION['authorized']);
			}
			else
			{
				$this->authorized = true;
				$this->username = $_SESSION['username'];
				return;
			}
		}
		$this->checkPostData();
	}
	
	function isAuthorized()
	{
		return $this->authorized;
	}
	
	function getUser()
	{
		if ($this->authorized)
			return $this->username;
		return false;
	}
	
	/**
	 * Check if we get data from form with username and password
	 *
	 * @return boolean
	 */
	function checkPostData()
	{
		if (empty($_POST))
			return false;
		
		if(isset($_POST[self::USERNAME_FIELD]) AND isset($_POST[self::PASSWORD_FIELD]))
		{
			//We get something from form!
			if ($this->checkUser($_POST[self::USERNAME_FIELD], $_POST[self::PASSWORD_FIELD]))
			{
				$this->authorized = true;
				$_SESSION['authorized'] = true;
				$this->username = $_POST[self::USERNAME_FIELD];
				$_SESSION['username'] = $_POST[self::USERNAME_FIELD];
				return true;
			}
			return false;
		}
		return false;
	}
	
	//You can overload this!
	protected function checkUser($username, $password)
	{
		global $config;
		//Check if this admin
		if
		(
			(strcasecmp($config['admin']['login'],   trim($username)) == 0)
		and
			(strcasecmp($config['admin']['password'], trim($password)) == 0)
		)
			return true;
		else
			return false;
	}
	
}
?>