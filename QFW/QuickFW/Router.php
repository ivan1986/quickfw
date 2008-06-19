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
	protected $cModule, $cControllerName, $cController, $cClass;
	public $module, $controller, $action;
	
	public $UriPath, $CurPath, $ParentPath;
	
	function __construct($baseDir='../application')
	{
		$this->baseDir = rtrim($baseDir, '/\\');		
		$this->module = '';
		$this->controller = NULL;
		$this->action = '';
	}

	function route($requestUri = null, $type='Action')
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

		$MCA = $this->loadMCA($data,$type);
		if (isset($MCA['Error']))
		{
			if ($GLOBALS['config']['release'])
				$this->show404();
			else
				die("Был выполнен запрос \t\t".$requestUri."\nадрес был разобран в\t\t ".
					$MCA['Path']."\n".
					$MCA['Error']);
		}
		$params = $this->parseParams($data);
		
		$this->cModule = $this->module = $MCA['Module'];
		$this->cControllerName = $this->controller = $MCA['Controller'];
		$this->CurPath = $this->UriPath = $MCA['Path'];
		$this->ParentPath = null;
		
		$this->action = $MCA['Action'];
		$this->cClass = $MCA['Class'];
		
		$this->cController = new $this->cClass();
		
		$CacheInfo=false;
		if ($MCA['cache'])
		{
			$CacheInfo=$this->cController->CacheInfo($this->action,$params);
			if (array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
			$data = $CacheInfo['Cacher']->load($CacheInfo['id']);
			if ($data)
			{
				if (array_key_exists('full',$CacheInfo))
					echo $data;
				else
				{
					$view->setScriptPath($this->baseDir.'/'.$this->module.'/templates');
					$view->displayMain($result);
				}
				return;
			}
		}
		
		$view->setScriptPath($this->baseDir.'/'.$this->module.'/templates');
		
		if (!empty($params))
			$result = call_user_func_array(array($this->cController, $this->action), $params);
		else
			$result = call_user_func(array($this->cController, $this->action));

		if ($CacheInfo && array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
		{
			if (array_key_exists('full',$CacheInfo))
				$result=$view->displayMain($result);
			
	 		if (array_key_exists('time',$CacheInfo))
			 	$CacheInfo['Cacher']->save($result,$CacheInfo['id'],
			 		array_key_exists('tags',$CacheInfo)?$CacheInfo['tags']:array(),
			 		$CacheInfo['time']
		 		);
		 	else 
			 	$CacheInfo['Cacher']->save($result,$CacheInfo['id'],
			 		array_key_exists('tags',$CacheInfo)?$CacheInfo['tags']:array()
		 		);
		}
		else 
			$view->displayMain($result);
		
	}
	
	function moduleRoute($Uri)
	{
		global $config;
		$patt=array();
		$MCA=array();

		if ($config['redirection']['useModuleRewrite'])
			$Uri = $this->rewrite($Uri);
		
		//два варианта записи вызова
		// module.controller.action(p1,p2,p3,...)
		if (preg_match('|(?:(.*?)\.)?(.*?)(?:\.(.*))?\((.*)\)|',$Uri,$patt))
		{
			$MCA=$this->loadMCA(array_slice($patt,1,3),'Module');
			$MCA['Params']=$this->parseScobParams($patt[4]);
		}
		else 
		{
			// module/controller/action/p1/p2/p3/...
			$data = split(self::URI_DELIMITER, $Uri);
			$MCA = $this->loadMCA($data,'Module');
			$MCA['Params']=$this->parseParams($data);
		}

		QuickFW_Module::addStartControllerClass($this->cClass,$this->cController);
		return $MCA;
	}

	function show404()
	{
		global $view;
		header("HTTP/1.1 404 Not Found");
		$view->setScriptPath(APPPATH.'/default/templates/');
		die($view->render('404.html'));
	}
	
	function delDef($url)
	{
		$url = explode('/',$url);
		if (isset($url[0]) && $url[0]==self::DEFAULT_MODULE)
			array_shift($url);
		if (count($url)==2 && $url[1]==self::DEFAULT_ACTION)
		{
			$url[0]=array_shift($url);
			if (isset($url[0]) && $url[0]==self::DEFAULT_CONTROLLER)
				array_shift($url);
		}
		return join('/',$url);
	}
	
	function redirectMCA($MCA,$tail='')
	{
		global $config;
		$base   = $config['redirection']['baseUrl'];
		$index  = $config['redirection']['useIndex']?'index.php/':'';
		$url    = $config['redirection']['useRewrite']?$this->backrewrite($MCA):$MCA;
		$defext = $config['redirection']['defExt'];

		header('Location: '.$base.$index.$url.($url!==''?$defext:'').$tail);
		exit();
	}

	function redirect($url)
	{
		header('Location: '.$url);
		exit();
	}

	function backrewrite($uri)
	{
		global $config;
		if (!$config['redirection']['useRewrite'])
			return $uri;
		if (!$this->rewriter)
		{
			require QFWPATH.'/QuickFW/Rewrite.php';
			$this->rewriter = new QuickFW_Rewrite();
		}
		return $this->rewriter->back($uri);
	}
	
	protected function rewrite($uri)
	{
		global $config;
		if (!$config['redirection']['useRewrite'])
			return $uri;
		if (!$this->rewriter)
		{
			require QFWPATH.'/QuickFW/Rewrite.php';
			$this->rewriter = new QuickFW_Rewrite();
		}
		return $this->rewriter->forward($uri);
	}
	
	protected function parseParams(&$data)
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
	
	protected function parseScobParams($par)
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
	
	protected function loadMCA(&$data,$type)
	{
		$MCA = array();
		while (isset($data[0]) AND $data[0] === '') array_shift($data);
		
		//Determine Module
		if (isset($data[0]) && (is_dir($this->baseDir . '/' . $data[0])))
			$MCA['Module'] = array_shift($data);
		else 
			$MCA['Module'] = $isModule ? $this->cModule : self::DEFAULT_MODULE;
		$path = $this->baseDir.'/'.$MCA['Module'];
		
		$c=count($data);	// Количество элементов URI исключая модуль
		//Determine Controller
		$cname = isset($data[0])?$data[0]: ($isModule ? $this->cController : self::DEFAULT_CONTROLLER);

		$class=ucfirst($cname).'Controller';
		$fullname = $path . '/controllers/' . strtr($class,'_','/') . '.php';
		
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
		$MCA['Action'] = strtr($aname,'.','_').$type;
		
		$actions=get_class_methods($class);
		$MCA['cache']= in_array('CacheInfo',$actions);
		if (in_array($MCA['Action'],$actions))
		{
			array_shift($data);
		}
		else
		{
			$aname=self::DEFAULT_ACTION;
			$MCA['Action'] = $aname.$type;
			if (!in_array($MCA['Action'],$actions))
			{
				$MCA['Error']="в классе \t\t\t".$class." \nне найдена функция \t\t".
				$MCA['Action']."\nМетод не найден шоб его";
				$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/'.$aname;
				return $MCA;
			}
		}
		
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
			if ($l>0)
			if (!substr_compare($uri,$config['redirection']['defExt'],
				$l,$len_de))
				$uri = substr($uri,0,$l);
		}
		$uri = trim($uri, self::URI_DELIMITER);
		return $uri;
	}
	
}
?>
