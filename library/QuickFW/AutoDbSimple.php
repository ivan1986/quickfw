<?php

class QuickFW_AutoDbSimple
{
	protected $DbSimple;
	
	protected $_driver, $_user, $_pass, $_host, $_database, $_prefix, $_encoding;
	
	function __construct($user = '', $pass = '', $database = '', $prefix = '', $driver = 'mysql', $host = 'localhost', $encoding = 'utf-8')
	{
		$this->DbSimple  = null;
		$this->_user 	 = $user;
		$this->_pass 	 = $pass;
		$this->_database = $database;
		$this->_prefix	 = $prefix;
		$this->_driver	 = $driver;
		$this->_host	 = $host;
		$this->_encoding = $encoding;
	}
	
	function __get($varname)
	{
		$varname = '_' . $varname;
		if (isset($this->$varname))
			return $this->$varname;
		else 
			return null;
	}
	
	function __set($varname, $value)
	{
		$varname = '_' . $varname;
		if (isset($this->$varname))
			$this->$varname = $value;
	}
	
	function __call($method, $params)
	{
		if ($this->DbSimple === null)
		{
			require LIBPATH.'/DbSimple/'.ucfirst($this->_driver).'.php';
			//require LIBPATH.'/DbSimple/Generic.php';
			$this->connect();
		}
		$result = call_user_func_array(array(&$this->DbSimple, $method), $params);
		return $result;
	}
	
	protected function connect()
	{
		$this->DbSimple = DbSimple_Generic::connect("{$this->_driver}://{$this->_user}:{$this->_pass}@{$this->_host}/{$this->_database}");
		$this->DbSimple->setErrorHandler(array(&$this, 'errorHandler'));
		$this->DbSimple->setIdentPrefix($this->_prefix);
		$this->query("SET NAMES ".$this->_encoding);
	}
	
	public function errorHandler($msg, $info)
	{
		// Если использовалась @, ничего не делать.
		if (!error_reporting()) return;
		// Выводим подробную информацию об ошибке. 
		echo "SQL Error: $msg<br><pre>"; 
		print_r($info);
		echo "</pre>";
		exit();
	}

}
?>