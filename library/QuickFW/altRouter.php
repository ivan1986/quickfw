<?php
class QuickFW_Router
{
	const URI_DELIMITER = '/';
	const DEFAULT_MODULE = 'default';
	const DEFAULT_CONTROLLER = 'index';
	const DEFAULT_ACTION = 'index';
	
	protected $baseDir;
	
	public $module, $controller, $action;
	
	function __construct($baseDir='../application')
	{
		$this->baseDir = rtrim($baseDir, '/\\');		
		$this->module = '';
		$this->controller = NULL;
		$this->action = '';
	}
	
	function route($requestUri = null)
	{
		global $view;
		if ($requestUri === null)
		{
			$requestUri = $_SERVER['REQUEST_URI'];
		}
		$this->filterUri($requestUri);
		
		$data = split(self::URI_DELIMITER, $requestUri);
		while (isset($data[0]) AND $data[0] === '') array_shift($data);
		//Determine Module
		if (isset($data[0]) && (is_dir($this->baseDir . '/' . $data[0])))
		{
			$this->module = $data[0];
			array_shift($data);
		}
		else 
		{
			$this->module = self::DEFAULT_MODULE;
		}
		
		$path = $this->baseDir . '/' . $this->module . '/controllers/';
		
		//Determine Controller
		if (isset($data[0]))
		{
			$cname = $data[0];
			$aspar = true;
		}
		else 
		{
			$cname = self::DEFAULT_CONTROLLER;
			$aspar = false;
		}
		
		$class=ucfirst($cname).'Controller';
		$file=$class.'.php';
		$fullname = $path . $file;
		
		if  (is_file($fullname))
		{
			require($fullname);
		}
		elseif(	($class=ucfirst(self::DEFAULT_CONTROLLER ).'Controller') && 
				($fullname = $path . 'IndexController.php') && 
				is_file($fullname))
		{
			require($fullname);
			$aspar = false;
		}
		else
		{
			header('HTTP/1.0 404 Not Found');
			exit();
		}
		
		$this->controller = new $class();
		if ($aspar) array_shift($data);
		//echo $class;
		
		if ($this->controller === NULL)
		{
			header("HTTP/1.0 404 Not Found");
			exit();
		}
		
		//Determine action
		if (isset($data[0]))
		{
			$aname = $data[0];
			$aspar = true;
		}
		else 
		{
			$aname = self::DEFAULT_ACTION;
			$aspar = false;
		}
		
		$aname.='Action';
		//echo $aname;
		if (method_exists($this->controller, $aname))
		{
			$this->action = $aname;
		}
		elseif (method_exists($this->controller, $aname = self::DEFAULT_ACTION.'Action'))
		{
			$this->action = $aname;
			$aspar = false;
		}
		else
		{
			header("HTTP/1.0 404 Not Found");
			exit();
		}
		if ($aspar) array_shift($data);
		
		//If array if not empty then its parameters
		/*
		while(!empty($data))
		{
			$_GET[$data[0]] = isset($data[1])?$data[1]:'';
			$_REQUEST[$data[0]] = isset($data[1])?$data[1]:'';
			array_shift($data);
			if (isset($data[0])) array_shift($data);
		}
		*/
		
		//call_user_func(array($this->controller, $this->action));
		if (!empty($data))
		{
			$data = array_map('urldecode', $data);
			call_user_func_array(array($this->controller, $this->action), $data);
		}
		else
			call_user_func(array($this->controller, $this->action));
	}
	
	function getCurModulePath()
	{
		return $this->baseDir.'/'.$this->module;
	}
	
	protected function filterUri(&$uri)
	{
		$pos = strpos($uri,'?');
		if ($pos !== false)
		{
			$uri = substr($uri,0,$pos);
		}
		$uri = trim($uri, self::URI_DELIMITER);
		return $uri;
	}
}
?>
