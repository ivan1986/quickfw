<?php

class QuickFW_Router
{
	protected $classes=array();

	protected $baseDir;
	protected $rewriter;

	protected $defM,$defC,$defA;

	//модуль и контроллер в контексте которого выполняется,
	//небходимо для роутинга компонентов
	protected $cModule, $cController;
	public $module, $controller, $action;

	public $UriPath, $CurPath, $ParentPath;

	function __construct($baseDir)
	{
		global $config;
		$this->baseDir = rtrim($baseDir, '/\\');
		$this->module = '';
		$this->controller = NULL;
		$this->action = '';
		$this->defM = $config['default']['module'];
		$this->defC = $config['default']['controller'];
		$this->defA = $config['default']['action'];
	}

	function route($requestUri = null, $type='Action')
	{
		global $view, $config;
		if ($requestUri === null)
			$requestUri = $_SERVER['REQUEST_URI'];
		$requestUri = $this->filterUri($requestUri);
		$requestUri = $this->rewrite($requestUri);

		$data = split('/', $requestUri);
		$data = array_map('urldecode', $data);

		$MCA = $this->loadMCA($data,$type);
		if (isset($MCA['Error']))
		{
			if ($config['release'])
				$this->show404();
			else
				die("Был выполнен запрос \t\t".$requestUri."\nадрес был разобран в\t\t ".
					$MCA['Path']."\n".
					$MCA['Error']);
		}
		$params = $this->parseParams($data);

		$this->cModule = $this->module = $MCA['Module'];
		$this->cController = $this->controller = $MCA['Controller'];
		$this->action = $MCA['Action'];
		$this->CurPath = $this->UriPath = $MCA['Path'];
		$this->ParentPath = null;

		$CacheInfo=false;
		if ($MCA['cache'])
		{
			$CacheInfo=$MCA['Class']->CacheInfo($this->action,$params);
			if (is_array($CacheInfo))
			{
				if (array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
				$data = $CacheInfo['Cacher']->load($CacheInfo['id']);
				$full=array_key_exists('full',$CacheInfo);
				if ($data)
				{
					if ($full)
						echo $data;
					else
					{
						$view->mainTemplate = $CacheInfo['Cacher']->load($CacheInfo['id'].'_MTPL');
						echo $view->displayMain($data);
					}
					return;
				}
			}
		}

		if (!empty($params))
			$result = call_user_func_array(array($MCA['Class'], $this->action), $params);
		else
			$result = call_user_func(array($MCA['Class'], $this->action));

		//Необходимо для вызовов всех деструкторов
		$this->classes=array();

		QFW::$view->setScriptPath($this->baseDir.'/'.$MCA['Module'].'/templates');

		if ($CacheInfo && array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
		{
			$par=array();
			$par[0]=$view->mainTemplate;
			$par[1]=$CacheInfo['id'].'_MTPL';
			$par[2]=array_key_exists('tags',$CacheInfo)?$CacheInfo['tags']:array();
			if (array_key_exists('time',$CacheInfo))
				$par[3]=$CacheInfo['time'];

			if ($full)
			{
				echo $result=$view->displayMain($result);
			}
			else
			{
				call_user_func_array(array($CacheInfo['Cacher'],'save'),$par);
				echo $view->displayMain($result);
			}

			$par[0]=$result;
			$par[1]=$CacheInfo['id'];
			call_user_func_array(array($CacheInfo['Cacher'],'save'),$par);
		}
		else
			echo $view->displayMain($result);
	}

	function blockRoute($Uri)
	{
		global $config;
		$patt=array();
		$MCA=array();

		if ($config['redirection']['useBlockRewrite'])
			$Uri = $this->rewrite($Uri);

		//два варианта записи вызова
		// module.controller.action(p1,p2,p3,...)
		if (preg_match('|(?:(.*?)\.)?(.*?)(?:\.(.*))?\((.*)\)|',$Uri,$patt))
		{
			$MCA=$this->loadMCA(array_slice($patt,1,3),'Block');
			$MCA['Params']=$this->parseScobParams($patt[4]);
		}
		else
		{
			// module/controller/action/p1/p2/p3/...
			$data = split('/', $Uri);
			$MCA = $this->loadMCA($data,'Block');
			$MCA['Params']=$this->parseParams($data);
		}

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
		if (isset($url[0]) && $url[0]==$this->defM)
			array_shift($url);
		if (count($url)==2 && $url[1]==$this->defA)
		{
			$url[0]=array_shift($url);
			if (isset($url[0]) && $url[0]==$this->defC)
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

	function redirect($url=null)
	{
		//если не указан - редирект откуда пришли
		$url=$url?$url:(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'');
		header('Location: '.$url);
		exit();
	}

	function backrewrite($uri)
	{
		if (!QFW::$config['redirection']['useRewrite'])
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
		if (!QFW::$config['redirection']['useRewrite'])
			return $uri;
		if (!$this->rewriter)
		{
			require QFWPATH.'/QuickFW/Rewrite.php';
			$this->rewriter = new QuickFW_Rewrite();
		}
		return $this->rewriter->forward($uri);
	}

	/**
	 * Парсит параметры, переданные как //p1/v1/p2/v2/p3
	 */
	protected function parseParams(&$data)
	{
		if (empty($data) || !empty($data[0]))
			return $data;
		array_shift($data);	//Удаляем первый пустой параметр
		while(!empty($data))
			$params[array_shift($data)] = array_shift($data);
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
			}
			elseif (!$instr && ($par[$i]=="'" || $par[$i]=='"'))
			{
				$instr=true;
				$strbeg=$par[$i];
			}
			elseif ($instr && ($par[$i]==$strbeg))
			{
				$instr=$par[$i-1]=='\\';
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
			$MCA['Module'] = $type=='Block' ? $this->cModule : $this->defM;
		if ($type!='Block')
			$this->cModule=$MCA['Module'];
		$path = $this->baseDir.'/'.$MCA['Module'];
		QFW::$view->setScriptPath($path.'/templates');

		$c=count($data);	// Количество элементов URI исключая модуль
		//Determine Controller
		$cname = isset($data[0])?$data[0]: ($type=='Block' ? $this->cController : $this->defC);
		if ($type!='Block')
			$this->cController=$cname;

		$class=ucfirst($cname).'Controller';
		$fullname = $path . '/controllers/' . strtr($class,'_','/') . '.php';

		if (is_file($fullname))
			array_shift($data);
		else
		{
			$cname=$this->defC;
			$class=ucfirst($cname).'Controller';
			$fullname = $path . '/controllers/' . $class . '.php';
			if (!is_file($fullname))
			{
				$MCA['Error']="не найден файл \t\t\t".$fullname."\nФайл не найден, твою дивизию...";
				$MCA['Path']=$MCA['Module'].'/...';
				return $MCA;
			}
		}
		$MCA['Controller'] = $cname;
		$class_key=$MCA['Module'].'|'.$MCA['Controller'];

		require_once($fullname);
		if (!array_key_exists($class_key,$this->classes))
			$this->classes[$class_key]=new $class;
		$MCA['Class'] = $this->classes[$class_key];

		if (!class_exists($class))
		{
			$MCA['Error']="не найден класс \t\t\t".$class."\nКласс не найден, мать его за ногу";
			$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/...';
			return $MCA;
		}

		$vars=get_class_vars($class);
		$actions=get_class_methods($class);
		$defA=array_key_exists('defA',$vars)?$vars['defA']:$this->defA;

		$aname = isset($data[0])?$data[0]:$defA;
		$MCA['Action'] = strtr($aname,'.','_').$type;

		$MCA['cache']= in_array('CacheInfo',$actions);
		if (in_array($MCA['Action'],$actions))
			array_shift($data);
		else
		{
			$aname=$defA;
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

	/**
	 * Проводит базовые преобразования Uri определенные в $config['redirection']
	 */
	protected function filterUri($uri)
	{
		global $config;
		$pos = strpos($uri,'?');
		if ($pos !== false)
			$uri = substr($uri,0,$pos);
		if (!substr_compare($uri,$config['redirection']['baseUrl'],0,strlen($config['redirection']['baseUrl'])))
			$uri = substr($uri,strlen($config['redirection']['baseUrl']));
		if ($config['redirection']['useIndex'] && strlen($uri)>=10 && !substr_compare($uri,'index.php/',0,10))
			$uri = substr($uri,10);
		if (!empty($config['redirection']['defExt']))
		{
			$len_de=strlen($config['redirection']['defExt']);
			$l=strlen($uri)-$len_de;
			if ($l>0 &&!substr_compare($uri,$config['redirection']['defExt'],$l,$len_de))
				$uri = substr($uri,0,$l);
		}
		return trim($uri, '/');
	}

}
?>
