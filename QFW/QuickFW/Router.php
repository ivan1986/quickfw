<?php

/**
 * Класс для работы с uri
 *
 * <br>редирект, прямой и обратный роутинг, загрузка контроллера
 * @package QFW
 */
class QuickFW_Router
{
	protected $classes=array();

	protected $baseDir;
	
	protected $defM,$defC,$defA;

	//модуль и контроллер в контексте которого выполняется,
	//необходимо для роутинга компонентов
	protected $cModule, $cController;

	/** @var string модуль, в контексте которого выполняется текущий запрос */
	public $module;
	
	/** @var string контроллер, в контексте которого выполняется текущий запрос */
	public $controller;

	/** @var string экшен, в контексте которого выполняется текущий запрос */
	public $action;

	/** 
	 * Uri который был вызван для исполнения
	 * Без учета параметров - только модуль, контроллер и экшен
	 *
	 * @var string 
	 */
	public $UriPath;

	/**
	 * Uri который выполняется в данный момент
	 * (отличается от вызванного в подключаемых модулях)
	 * 
	 * @var string
	 */
	public $CurPath;

	/**
	 * Uri из которого был вызван выполняемый модуль
	 *
	 * @var string
	 */
	public $ParentPath;

	/**
	 * Uri который был вызван для исполнения
	 * модуль, контроллер, экшен и параметры
	 *
	 * @var string
	 */
	public $Uri;

	/**
	 * Uri который был вызван для исполнения
	 * после фильтрации переменных и реврайта
	 *
	 * @var string
	 */
	public $RequestUri;

	public function __construct($baseDir)
	{
		$this->baseDir = rtrim($baseDir, '/\\');
		$this->module = '';
		$this->controller = NULL;
		$this->action = '';
		$this->defM = QFW::$config['default']['module'];
		$this->defC = QFW::$config['default']['controller'];
		$this->defA = QFW::$config['default']['action'];
	}

	/**
	 * Вызов Uri для исполнения
	 *
	 * @param string $requestUri запрашиваемый Uri
	 * @param string $type тип Uri (Action|Cli|...)
	 * @return null
	 */
	public function route($requestUri = null, $type='Action')
	{
		if ($requestUri === null)
			$requestUri = $_SERVER['REQUEST_URI'];
		$requestUri = $this->filterUri($requestUri);
		$requestUri = $this->rewrite($requestUri);

		$data = explode('/', $requestUri);
		$data = array_map('urldecode', $data);

		$MCA = $this->loadMCA($data,$type);
		if (isset($MCA['Error']))
		{
			if (QFW::$config['QFW']['release'])
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
		
		$result = call_user_func_array(array($MCA['Class'], $this->action), $params);
		
		QFW::$view->setScriptPath($this->baseDir.'/'.$MCA['Module'].'/templates');

		echo QFW::$view->displayMain($result);
	}

	/**
	 * Вызов блока с заданным Uri
	 *
	 * @internal
	 * @param string $Uri Uri блока
	 * @return string содержимое блока
	 */
	public function blockRoute($Uri)
	{
		$patt=array();

		if (QFW::$config['redirection']['useBlockRewrite'])
			$Uri = $this->rewrite($Uri);

		//два варианта записи вызова
		// module.controller.action(p1,p2,p3,...)
		if (preg_match('|(?:(.*?)\.)?(.*?)(?:\.(.*))?\((.*)\)|',$Uri,$patt))
		{
			$data = array_slice($patt,1,3);
			$MCA = $this->loadMCA($data,'Block');
			// Если вы все еще сидите на PHP 5.2 то раскомментируйте старый вариант
			$MCA['Params']=str_getcsv($patt[4],',',"'",'\\'); // $this->parseScobParams($patt[4]);
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

		list($lpPath, $this->ParentPath, $this->CurPath) =
			array($this->ParentPath, $this->CurPath, $MCA['Path']);

		$result = call_user_func_array(array($MCA['Class'], $MCA['Action']), $MCA['Params']);

		list($this->CurPath, $this->ParentPath) =
			array($this->ParentPath, $lpPath);

		return $result;
	}

	/**
	 * Необходимо для вызовов всех деструкторов
	 *
	 * @internal
	 */
	public function startDisplayMain() { $this->classes = array(); }

	/**
	 * Отображает страницу 404 и завершает выполнение скрипта
	 *
	 * @return die
	 */
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
	 *
	 * (напр: default/other/index => other)
	 *
	 * @param string $url Исходный Uri
	 * @return string Uri с удаленными компонентами
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

	/**
	 * Посылает заголовок Location для перехода на нужное действие
	 *
	 * @param string $MCA Uri нужного действия (модуль/контроллер/экшен)
	 * @param string|array $get GET парамерты или произвольный хвост
	 * @return exit
	 */
	public function redirectMCA($MCA, $get='')
	{
		$base   = QFW::$config['redirection']['baseUrl'];
		$index  = QFW::$config['redirection']['useIndex']?'index.php/':'';
		$url    = QFW::$config['redirection']['useRewrite']?$this->backrewrite($MCA):$MCA;
		$defext = QFW::$config['redirection']['defExt'];
		if (is_array($get) && count($get))
			$get = '?'.http_build_query($get);

		header('Location: '.$base.$index.$url.($url!==''?$defext:'').$get);
		exit();
	}

	/**
	 * Редирект на нужный урл или возвращает по рефереру
	 *
	 * @param string $url Url на который перейдет браузер
	 * @param boolean $ref переходить по HTTP_REFERER, если есть, иначе на $url
	 * @return exit
	 */
	public function redirect($url, $ref=false)
	{
		//если не указан - редирект откуда пришли
		if ($ref && isset($_SERVER['HTTP_REFERER']))
			$url = $_SERVER['HTTP_REFERER'];
		else
			$url = $url ? $url : '';
		header('Location: '.$url);
		exit();
	}

	/**
	 * Перезагрузка текущей страницы
	 *
	 * <br>Полностью аналогична "Location: $_SERVER['REQUEST_URI']"
	 *
	 * @return exit
	 */
	public function reload()
	{
		header('Location: '.$_SERVER['REQUEST_URI']);
		exit();
	}

	/** @var array Массив прямых преобразований Uri */
	protected $rewrite = array();
	/** @var array Массив обратных преобразований Uri */
	protected $backrewrite = array();
	
	/**
	 * Функция производит преобразования урла для вывода на страницу
	 *
	 * @internal
	 * @param string $uri Uri для бекреврайта
	 * @return string преобразованный Uri
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
	 * @internal
	 * @param string $uri Uri для реврайта
	 * @return string преобразованный Uri
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
	 *
	 * @internal
	 */
	protected function parseParams(&$data)
	{
		if (empty($data) || !empty($data[0]))
			return $data;
		array_shift($data); //Удаляем первый пустой параметр
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

	protected function loadMCA(&$data, $type)
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

		$c=count($data); // Количество элементов URI исключая модуль
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

		//Инициализируем те значения, которые мы уже знаем,
		//чтобы можно было узнать как нас вызвали в конструкторе
		if ($type != 'Block')
		{
			$this->module = $MCA['Module'];
			$this->controller = $MCA['Controller'];
		}

		if (!isset($this->classes[$class_key]))
		{
			require($fullname);
			if (!class_exists($class))
			{
				//Смотрим, а не в неймспейсе ли он случайно
				if (class_exists($MCA['Module'].'\\'.$class))
					$class = $MCA['Module'].'\\'.$class;
				else
				{
					$MCA['Error']="не найден класс \t\t\t".$class."\nКласс не найден, мать его за ногу";
					$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/...';
					return $MCA;
				}
			}
			$vars = get_class_vars($class);
			$acts = get_class_methods($class);
			
			//устанавливаем значение $this->action
			if ($type != 'Block')
			{
				$aname = isset($data[0]) ? $data[0] :
					(isset($vars['defA']) ? $vars['defA'] : $this->defA);
				$fname = strtr($aname,'.','_').$type;
				if (!in_array($fname,$acts))
					$fname = (isset($vars['defA']) ? $vars['defA'] : $this->defA).$type;
				$this->action = $fname;
			}
			
			$this->classes[$class_key] = array(
				'i'    => new $class,
				'defA' => isset($vars['defA']) ? $vars['defA'] : $this->defA,
				'a'    => $acts,
			);
		}
		$MCA['Class'] = $this->classes[$class_key]['i'];
		
		$aname = isset($data[0]) ? $data[0] : $this->classes[$class_key]['defA'];
		$MCA['Action'] = strtr($aname,'.','_').$type;
		
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

		if (count($data)==$c && $c>0) // если из URI после модуля ничего не забрали и что-то осталось
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
	 *
	 * @param string $uri Изначальный Uri
	 * @return string преобразованный Uri
	 */
	protected function filterUri($uri)
	{
		$pos = strpos($uri,'?');
		if ($pos !== false)
			$uri = substr($uri,0,$pos);
		if (strpos($uri,QFW::$config['redirection']['baseUrl']) === 0)
			$uri = substr($uri,strlen(QFW::$config['redirection']['baseUrl']));
		if (strpos($uri,'index.php/') === 0)
			$uri = substr($uri,10);
		if (!empty(QFW::$config['redirection']['defExt']))
		{
			$len_de=strlen(QFW::$config['redirection']['defExt']);
			$l=strlen($uri)-$len_de;
			if ($l>0 && strpos($uri,QFW::$config['redirection']['defExt'],$l)!==false)
				$uri = substr($uri,0,$l);
		}
		
		return trim($uri, '/');
	}

}
?>
