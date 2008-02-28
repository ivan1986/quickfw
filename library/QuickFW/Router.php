<?php
class QuickFW_Router
{
	const URI_DELIMITER = '/';
	const DEFAULT_MODULE = 'default';
	const DEFAULT_CONTROLLER = 'index';
	const DEFAULT_ACTION = 'index';
	
	protected $baseDir;

	//модуль и контроллер в контексте которого выполняется,
	//небходимо для роутинга компонентов
	protected $cModule, $cControllerName;
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
		$data = array_map('urldecode', $data);
		while (isset($data[0]) AND $data[0] === '') array_shift($data);

		$MCA = $this->getMCA($data,false);

		$this->module = $MCA['Module'];
		
		$this->cModule = $this->module;
		$this->cControllerName = $MCA['Controller'];
		
		$path = $this->getCurModulePath();

		$cname = $MCA['Controller'];
		
		$class=ucfirst($cname).'Controller';
		$file=$class.'.php';
		$fullname = $path . '/controllers/' . $file;
		
		if  (is_file($fullname))
		{
			require($fullname);
		}
		else
		{
			echo "Был выполнен запрос \t\t".$requestUri."\nне найден файл \t\t\t".$fullname."\n";
			die("Контроллер не найден, мать его за ногу");
		}
		
		$this->controller = new $class();
		
		if ($this->controller === NULL)
		{
			echo "Был выполнен запрос \t\t".$requestUri."\nв файле \t\t\t".$fullname."\nне найден класс \t\t\t".$class."\n";
			die("Контроллер не найден, мать его за ногу");
		}
		
		
		$aname = ucfirst($MCA['Action']) . 'Action';
		if (method_exists($this->controller, $aname))
		{
			$this->action = $aname;
		}
		else
		{
			echo "Был выполнен запрос \t\t".$requestUri."\n в файле \t\t\t".$fullname."\nнаходящемся в классе \t\t".$class." \nне найдена функция \t\t".$aname."\n";
			die("Действие не найдено шоб его");
		}
		
		$view->setScriptPath($path.'/templates');
		
		$params = $this->parceParams($data);

		if (!empty($params))
			$result = call_user_func_array(array($this->controller, $this->action), $params);
		else
			$result = call_user_func(array($this->controller, $this->action));

		$view->assign('content',$result);
		$view->displayMain();

	}
	
	function moduleRoute($Uri)
	{
		//два варианта записи вызова
		// module.controller.action(p1,p2,p3,...)
		$patt=array();
		$MCA=array();
		if (preg_match('|(?:(.*?)\.)?(.*?)(?:\.(.*))?\((.*)\)|',$Uri,$patt))
		{
			// юзаем дефолтовый module и action
			if (empty($patt[3]))
			{
				$MCA['Module']=$this->cModule;
				if (empty($patt[1]))
				{
					$MCA['Controller']=$patt[2];
					$MCA['Action']=self::DEFAULT_ACTION;
				}
				else
				{
					$MCA['Controller']=$patt[1];
					$MCA['Action']=$patt[2];
				}
			}
			else
			{
				$MCA['Module']=$patt[1];
				$MCA['Controller']=$patt[2];
				$MCA['Action']=$patt[3];
			}
			$MCA['Params']=$this->parceScobParams($patt[4]);
		}
		else 
		{
			// module/controller/action/p1/p2/p3/...
			$data = split(self::URI_DELIMITER, $Uri);
			$data = array_map('urldecode', $data);
			while (isset($data[0]) AND $data[0] === '') array_shift($data);
	
			$MCA = $this->getMCA($data);
			$MCA['Params']=$this->parceParams($data);
		}
		$MCA['File']=$this->baseDir.'/'.$MCA['Module'].'/controllers/'.ucfirst($MCA['Controller']).'Controller.php';
		return $MCA;
	}
	
	function getCurModulePath()
	{
		return $this->baseDir.'/'.$this->module;
	}
	
	function redirect($url)
	{
		header('Location: '.$url);
		exit();
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

	protected function getMCA(&$data,$isModule=true)
	{
		//Determine Module
		if (isset($data[0]) && (is_dir($this->baseDir . '/' . $data[0])))
		{
			$module = $data[0];
			array_shift($data);
		}
		else 
		{
			$module = $isModule ? $this->cModule : self::DEFAULT_MODULE;
		}
		
		//Determine Controller
		if (isset($data[0]))
		{
			$cname = $data[0];
			array_shift($data);
		}
		else 
		{
			$cname = $isModule ? $this->cController : self::DEFAULT_CONTROLLER;
		}
		
		//Determine Action
		if (isset($data[0]))
		{
			$aname = $data[0];
			array_shift($data);
		}
		else 
		{
			$aname = self::DEFAULT_ACTION;
		}

		return array(
			'Module'=> $module,
			'Controller'=> $cname,
			'Action'=> $aname,
		);

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
