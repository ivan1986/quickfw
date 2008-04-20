<?php

class QuickFW_Router
{
	const URI_DELIMITER = '/';
	const DEFAULT_MODULE = 'default';
	const DEFAULT_CONTROLLER = 'index';
	const DEFAULT_ACTION = 'index';
	
	protected $baseDir;
	protected $rewriter;

	//модуль и контроллер в контексте которого выполняется,
	//небходимо для роутинга компонентов
	protected $cModule, $cControllerName;
	public $module, $controller, $action;
	
	public $UriPath, $CurPath, $ParentPath;
	
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
		$requestUri = $this->filterUri($requestUri);
		$requestUri = $this->rewrite($requestUri);
		
		$data = split(self::URI_DELIMITER, $requestUri);
		$data = array_map('urldecode', $data);

		$MCA = $this->loadMCA($data,false);
		if (isset($MCA['Error']))
		{
			die("Был выполнен запрос \t\t".$requestUri."\nадрес был разобран в\t\t ".
				$MCA['Path']."\n".
				$MCA['Error']);
		}
		
		$this->cModule = $this->module = $MCA['Module'];
		$this->cControllerName = $this->controller = $MCA['Controller'];
		$this->action = $MCA['Action'];
		$class = $MCA['Class'];
		
		$controller = new $class();
		$action = $MCA['Action'];
		
		$this->CurPath = $this->UriPath = $MCA['Path'];
		$this->ParentPath = null;

		$view->setScriptPath($this->baseDir.'/'.$this->module.'/templates');
		
		$params = $this->parceParams($data);

		if (!empty($params))
			$result = call_user_func_array(array($controller, $action), $params);
		else
			$result = call_user_func(array($controller, $action));

		$view->assign('content',$result);
		$view->displayMain();

	}
	
	function moduleRoute($Uri)
	{
		//два варианта записи вызова
		// module.controller.action(p1,p2,p3,...)
		$patt=array();
		$MCA=array();
		$Uri = $this->rewrite($Uri);
		if (preg_match('|(?:(.*?)\.)?(.*?)(?:\.(.*))?\((.*)\)|',$Uri,$patt))
		{
			$MCA=$this->loadMCA(array_slice($patt,1,3));
			$MCA['Params']=$this->parceScobParams($patt[4]);
		}
		else 
		{
			// module/controller/action/p1/p2/p3/...
			$data = split(self::URI_DELIMITER, $Uri);
			$MCA = $this->loadMCA($data);
			$MCA['Params']=$this->parceParams($data);
		}
		return $MCA;
	}
	
	function redirect($url)
	{
		header('Location: '.$url);
		exit();
	}

	function backroute($url)
	{
		global $config;
		if (!$config['redirection']['useRewrite'])
			return $url;
		if (!$this->rewriter)
		{
			require LIBPATH.'/QuickFW/Rewrite.php';
			$this->rewriter = new QuickFW_Rewrite();
		}
		return $this->rewriter->back($url);
	}
	
	protected function rewrite($uri)
	{
		global $config;
		if (!$config['redirection']['useRewrite'])
			return $url;
		if (!$this->rewriter)
		{
			require LIBPATH.'/QuickFW/Rewrite.php';
			$this->rewriter = new QuickFW_Rewrite();
		}
		return $this->rewriter->forword($uri);
	}
	
	protected function parceParams(&$data)
	{
		if (empty($data))
			return array();
		if ($data[0]!='')
			return $data;
		array_shift($data);
		//If array if not empty then its parameters
		while(!empty($data))
		{
			$params[$data[0]] = isset($data[1])?$data[1]:'';
			array_shift($data);
			if (isset($data[0])) array_shift($data);
		}
		return array('params' => $params);
	}
	
	protected function parceScobParams($par)
	{
		$instr  = false;
		$strbeg = '';
		$params = array();
		$startpar=0;
		for($i=0;$i<strlen($par);$i++)
		{
			if (!$instr && $par[$i]==',')
			{
				$params[]=substr($par,$startpar,$i-$startpar);
				$startpar=$i+1;
				continue;
			}
			if (!$instr && ($par[$i]=="'" || $par[$i]=='"'))
			{
				$instr=true;
				$strbeg=$par[$i];
				continue;
			}
			if ($instr && ($par[$i]==$strbeg))
			{
				$instr=$par[$i-1]=='\\';
				continue;
			}
		}
		$params[]=substr($par,$startpar);
		foreach ($params as $k=>$v)
		{
			//при таком разборе если строка начинается кавычкой она ей обязательно заканчивается (?)
			if (($v[0]=="'" || $v[0]=='"') )//&& $v[0]==$v[strlen($v)-1])
			{
				$v=str_replace(
					array('\\\\','\\'.$v[0]),
					array('\\'  ,$v[0]),
					$v);
				$params[$k]=substr($v,1,strlen($v)-2);
			}
		}
		return $params;
	}
	
	protected function loadMCA(&$data,$isModule=true)
	{
		$MCA = array();
		while (isset($data[0]) AND $data[0] === '') array_shift($data);
		//Determine Module
		if (isset($data[0]) && (is_dir($this->baseDir . '/' . $data[0])))
		{
			$MCA['Module'] = $data[0];
			array_shift($data);
		}
		else 
		{
			$MCA['Module'] = $isModule ? $this->cModule : self::DEFAULT_MODULE;
		}
		$path = $this->baseDir.'/'.$MCA['Module'];
		
		$c=count($data);	// Количество элементов URI исключая модуль
		//Determine Controller
		$cname = isset($data[0])?$data[0]: ($isModule ? $this->cController : self::DEFAULT_CONTROLLER);

		$class=ucfirst($cname).'Controller';
		$fullname = $path . '/controllers/' . $class . '.php';
		
		if  (is_file($fullname))
		{
			array_shift($data);
		}
		else
		{
			$cname=self::DEFAULT_CONTROLLER;
			$class=ucfirst($cname).'Controller';
			$fullname = $path . '/controllers/' . $class . '.php';
		}

		require_once($fullname);
		$MCA['Controller'] = $cname;
		$MCA['Class'] = $class;

		if (!class_exists($class))
		{
			$MCA['Error']="не найден класс \t\t\t".$class."\nКласс не найден, мать его за ногу";
			$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/...';
			return $MCA;
		}

		$aname = isset($data[0])?$data[0]:self::DEFAULT_ACTION;
		$action = $aname.($isModule?'Module':'Action');
		
		$actions=get_class_methods($class);
		if (in_array($action,$actions))
		{
			array_shift($data);
		}
		else
		{
			$aname=self::DEFAULT_ACTION;
			$action = $aname.($isModule?'Module':'Action');
			if (!in_array($action,$actions) && !$isModule)
			{
				$MCA['Error']="в классе \t\t\t".$class." \nне найдена функция \t\t".
				$aname.($isModule?'Module':'Action')
				."\nМетод не найден шоб его";
				$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/'.$aname;
				return $MCA;
			}
		}
		$MCA['Action'] = $aname.($isModule?'Module':'Action');
		if (count($data)==$c && $c>0)	// если из URI после модуля ничего не забрали и что-то осталось
		{
			$MCA['Error']="Указаны параметры у дефолтового CA \n".
				"или несуществующий Контроллер или Экшен дефолтового контроллера\n".
				"Не работает, мать его за ногу";
		}
		$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/'.$aname;
		
		return $MCA;
	}

	protected function filterUri($uri)
	{
		global $config;
		$pos = strpos($uri,'?');
		if ($pos !== false)
		{
			$uri = substr($uri,0,$pos);
		}
		if (!substr_compare($uri,$config['redirection']['baseUrl'],0,strlen($config['redirection']['baseUrl'])))
		{
			$uri = substr($uri,strlen($config['redirection']['baseUrl']));
		}
		if ($config['redirection']['useIndex'] && strlen($uri)>=10 && !substr_compare($uri,'index.php/',0,10))
		{
			$uri = substr($uri,10);
		}
		if ($config['redirection']['defExt'] != '')
		{
			$len_de=strlen($config['redirection']['defExt']);
			$l=strlen($uri)-$len_de;
			if (!substr_compare($uri,$config['redirection']['defExt'],
				$l,$len_de))
				$uri = substr($uri,0,$l);
		}
		$uri = trim($uri, self::URI_DELIMITER);
		return $uri;
	}
	
}
?>
