<?php

class QuickFW_Router
{
	protected $classes=array();

	protected $baseDir;
	
	protected $defM,$defC,$defA;

	//модуль и контроллер в контексте которого выполняется,
	//необходимо для роутинга компонентов
	protected $cModule, $cController;
	public $module, $controller, $action;

	public $UriPath, $CurPath, $ParentPath, $Uri, $RequestUri;

	public function __construct($baseDir)
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

	public function route($requestUri = null, $type='Action')
	{
		global $view, $config;
		if ($requestUri === null)
			$requestUri = $_SERVER['REQUEST_URI'];
		$requestUri = $this->filterUri($requestUri);
		$requestUri = $this->rewrite($requestUri);

		$data = explode('/', $requestUri);
		$data = array_map('urldecode', $data);

		$MCA = $this->loadMCA($data,$type);
		if (isset($MCA['Error']))
		{
			if ($config['QFW']['release'])
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
		$this->Uri = $MCA['Path'].'/'.join('/',$data);
		$this->RequestUri = $requestUri;
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
		
		$result = call_user_func_array(array($MCA['Class'], $this->action), $params);
		
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

	public function blockRoute($Uri)
	{
		global $config;
		$patt=array();

		if ($config['redirection']['useBlockRewrite'])
			$Uri = $this->rewrite($Uri);

		//два варианта записи вызова
		// module.controller.action(p1,p2,p3,...)
		if (preg_match('|(?:(.*?)\.)?(.*?)(?:\.(.*))?\((.*)\)|',$Uri,$patt))
		{
			$MCA = array_slice($patt,1,3);
			$MCA = $this->loadMCA($MCA,'Block');
			$MCA['Params']=$this->parseScobParams($patt[4]);
		}
		else
		{
			// module/controller/action/p1/p2/p3/...
			$data = explode('/', $Uri);
			$MCA = $this->loadMCA($data,'Block');
			$MCA['Params']=$this->parseParams($data);
		}
		if (isset($MCA['Error']))
			return "Ошибка подключения блока ".$Uri." адрес был разобран в\t\t ".
				$MCA['Path']."\n".$MCA['Error'];

		$CacheInfo=false;
		if ($MCA['cache'])
		{
			$CacheInfo=$MCA['Class']->CacheInfo($MCA['Action'],$MCA['Params']);
			if (is_array($CacheInfo))
			{
				if (array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
				$data = $CacheInfo['Cacher']->load($CacheInfo['id']);
				if ($data)
					return $data;
			}
		}

		list($lpPath, $this->ParentPath, $this->CurPath) =
			array($this->ParentPath, $this->CurPath, $MCA['Path']);

		$result = call_user_func_array(array($MCA['Class'], $MCA['Action']), $MCA['Params']);

		list($this->CurPath, $this->ParentPath) =
			array($this->ParentPath, $lpPath);

		if (is_array($CacheInfo))
		{
			if (array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
			{
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
		}

		return $result;
	}

	//Необходимо для вызовов всех деструкторов
	public function startDisplayMain() { $this->classes = array(); }

	public function show404()
	{
		$GLOBALS['DONE'] = 1;
		//TODO: php_sapi_name если через nginx, то Status:
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
		QFW::$view->setScriptPath(APPPATH.'/default/templates/');
		die(QFW::$view->render('404.html'));
	}

	/**
	 * Удаляет из адреса дефолтовые компоненты
	 * (напр: default/other/index => other)
	 */
	public function delDef($url)
	{
		$url = explode('/',$url);
		$n = min(3, count($url));
		if (isset($url[0]) && $url[0]==$this->defM && $n--)
			array_shift($url);
		if (count($url)>$n)
			return join('/',$url);
		$i = $n - 1;
		if (count($url)<=$n && isset($url[$i]) && $url[$i]==$this->defA)
		{
			unset($url[$i]);
			if (isset($url[0]) && $url[0]==$this->defC)
				array_shift($url);
		}
		return join('/',$url);
	}

	public function redirectMCA($MCA,$tail='')
	{
		global $config;
		$base   = $config['redirection']['baseUrl'];
		$index  = $config['redirection']['useIndex']?'index.php/':'';
		$url    = $config['redirection']['useRewrite']?$this->backrewrite($MCA):$MCA;
		$defext = $config['redirection']['defExt'];

		header('Location: '.$base.$index.$url.($url!==''?$defext:'').$tail);
		exit();
	}

	public function redirect($url=null)
	{
		//если не указан - редирект откуда пришли
		$url = $url ? $url : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
		header('Location: '.$url);
		exit();
	}

	public function reload()
	{
		header('Location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	protected $rewrite = array();
	protected $backrewrite = array();
	
	/**
	 * Функция производит преобразования урла для вывода на страницу
	 *
	 * @param string $uri
	 * @return string
	 */
	public function backrewrite($uri)
	{
		if (!QFW::$config['redirection']['useRewrite'])
			return $uri;
		if (!$this->backrewrite)
		{
			$backrewrite = array();
			require APPPATH . '/rewrite.php';
			$this->backrewrite = $backrewrite;
		}
		return preg_replace(array_keys($this->backrewrite), array_values($this->backrewrite), $uri);
	}

	/**
	 * Функция производит преобразования урла при запросе
	 *
	 * @param string $uri
	 * @return string
	 */
	protected function rewrite($uri)
	{
		if (!QFW::$config['redirection']['useRewrite'])
			return $uri;
		if (!$this->rewrite)
		{
			$rewrite = array();
			require APPPATH . '/rewrite.php';
			$this->rewrite = $rewrite;
		}
		return preg_replace(array_keys($this->rewrite), array_values($this->rewrite), $uri);
	}

	/**
	 * Парсит параметры, переданные как //p1/v1/p2/v2/p3
	 */
	protected function parseParams(&$data)
	{
		if (empty($data) || !empty($data[0]))
			return $data;
		array_shift($data);	//Удаляем первый пустой параметр
		$params = array();
		while(!empty($data))
			$params[array_shift($data)] = array_shift($data);
		return array('params' => $params);
	}

	protected function parseScobParams($par)
	{
//регулярка для парсинга параметров - записана так, чтобы не было страшных экранировок
$re = <<<SREG
#\s*([^,"']+|"(?:[^"]|\\"|"")*?[^\"]"|'(?:[^']|\\'|'')*?[^\']')\s*(?:,|$)#
SREG;
		$m=array();
		preg_match_all($re, $par, $m);
		foreach ($m[1] as &$v)
			$v = str_replace(array('""',"''",'\"',"\'"), array('"',"'",'"',"'"),
				trim($v,'\'" '));
		return $m[1];
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

		if (!isset($this->classes[$class_key]))
		{
			require($fullname);
			if (!class_exists($class))
			{
				$MCA['Error']="не найден класс \t\t\t".$class."\nКласс не найден, мать его за ногу";
				$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/...';
				return $MCA;
			}
			$vars = get_class_vars($class);
			$acts = get_class_methods($class);
			$this->classes[$class_key] = array(
				'i'    => new $class,
				'defA' => isset($vars['defA']) ? $vars['defA'] : $this->defA,
				'a'    => $acts,
				'c'    => in_array('CacheInfo',$acts),
			);
		}
		$MCA['Class'] = $this->classes[$class_key]['i'];

		$aname = isset($data[0]) ? $data[0] : $this->classes[$class_key]['defA'];
		$MCA['Action'] = strtr($aname,'.','_').$type;

		$MCA['cache'] = $this->classes[$class_key]['c'];
		if (in_array($MCA['Action'],$this->classes[$class_key]['a']))
			array_shift($data);
		else
		{
			$aname = $this->classes[$class_key]['defA'];
			$MCA['Action'] = $aname.$type;
			if (!in_array($MCA['Action'],$this->classes[$class_key]['a']))
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
		if (strpos($uri,$config['redirection']['baseUrl']) === 0)
			$uri = substr($uri,strlen($config['redirection']['baseUrl']));
		if ($config['redirection']['useIndex'] && strpos($uri,'index.php/') === 0)
			$uri = substr($uri,10);
		if (!empty($config['redirection']['defExt']))
		{
			$len_de=strlen($config['redirection']['defExt']);
			$l=strlen($uri)-$len_de;
			if ($l>0 && strpos($uri,$config['redirection']['defExt'],$l)!==false)
				$uri = substr($uri,0,$l);
		}
		
		return trim($uri, '/');
	}

}
?>
