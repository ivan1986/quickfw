<?php

/**
 * Класс для работы с uri
 *
 * <br>редирект, прямой и обратный роутинг, загрузка контроллера
 * 
 * @package QFW
 */
class QuickFW_Router
{
	/**
	 * @var string	Символ разделитель компонентов в запросе
	 * @example Бывает очень полезно в случае переписывании на движок
	 * с какой-то поделки в случае кривой относительной адресации
	 */
	const PATH_SEPARATOR = '/';

	protected $classes=array();

	protected $baseDir;
	
	protected $defM,$defC,$defA;

	//модуль и контроллер в контексте которого выполняется,
	//необходимо для роутинга компонентов
	protected $curModule, $curController;

	/** @var string модуль, в контексте которого выполняется текущий запрос */
	public $module;
	
	/** @var string контроллер, в контексте которого выполняется текущий запрос */
	public $controller;

	/** @var string экшен, в контексте которого выполняется текущий запрос */
	public $action;

	/** @var string тип, в контексте которого выполняется текущий запрос */
	public $type;

	/** @var string текущий модуль */
	public $cModule;

	/** @var string текущий контроллер */
	public $cController;

	/** @var string текущий экшен */
	public $cAction;

	/**
	 * @var string Uri который был вызван для исполнения
	 * <br>Без учета параметров - только модуль, контроллер и экшен
	 */
	public $UriPath;

	/**
	 * @var string Uri который выполняется в данный момент
	 * <br>(отличается от вызванного в подключаемых модулях)
	 */
	public $CurPath;

	/** @var string Uri из которого был вызван выполняемый модуль */
	public $ParentPath;

	/** @var string Uri который был вызван для исполнения модуль, контроллер, экшен и параметры */
	public $Uri;

	/** @var string Uri который был вызван для исполнения после фильтрации переменных и реврайта */
	public $RequestUri;

	public function __construct($baseDir)
	{
		$this->baseDir = rtrim($baseDir, '/\\');
		$this->module = '';
		$this->controller = '';
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

		$data = explode(self::PATH_SEPARATOR, $requestUri);
		$data = array_map('urldecode', $data);

		//обнуляем модуль - если нас вызвали повторно
		$this->module = '';

		$MCA = $this->loadMCA($data, $type);
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

		$this->curModule = $this->cModule = $this->module = $MCA['Module'];
		$this->curController = $this->cController = $this->controller = $MCA['Controller'];
		$this->cAction = $this->action = $MCA['Action'];
		$this->CurPath = $this->UriPath = $MCA['Path'];
		$this->Uri = $MCA['Path'] . self::PATH_SEPARATOR . join(self::PATH_SEPARATOR, $data);
		$this->RequestUri = $requestUri;
		$this->ParentPath = null;
		
		$result = call_user_func_array(array($MCA['Class'], $MCA['Action'].$MCA['Type']), $params);
		
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

		//Сохраняем старый путь шаблонов
		$scriptPath = QFW::$view->getScriptPath();
		//сохраняем прошлый MCA
		list ($oModule, $oController, $oAction) =
			array($this->cModule, $this->cController, $this->cAction);

		if ($Uri instanceof Url)
			$Uri = $Uri->intern();

		//два варианта записи вызова
		// module.controller.action(p1,p2,p3,...)
		if (preg_match('|^(?:(\w*?)\.)?(\w*?)(?:\.(\w*))?(?:\((.*)\))?$|',$Uri,$patt))
		{
			$data = array_slice($patt,1,3);
			$MCA = $this->loadMCA($data,'Block');
			// Если вы все еще сидите на PHP 5.2 то раскомментируйте старый вариант
			$MCA['Params'] = empty($patt[4]) ? array() :
				str_getcsv($patt[4],',',"'",'\\'); // $this->parseScobParams($patt[4]);
		}
		else
		{
			//реврайт имеет смысл только для вызовов, записанных через слеши
			if (QFW::$config['redirection']['useBlockRewrite'])
				$Uri = $this->rewrite($Uri);
			// module/controller/action/p1/p2/p3/...
			$data = explode(self::PATH_SEPARATOR, $Uri);
			$MCA = $this->loadMCA($data,'Block');
			$MCA['Params'] = $this->parseParams($data);
		}

		if (isset($MCA['Error']))
		{
			//восстанавливаем MCA
			list ($this->cModule, $this->cController, $this->cAction) =
				array($oModule, $oController, $oAction);
			//Возвращаем путь к шаблонам после вызова
			QFW::$view->setScriptPath($scriptPath);

			if (QFW::$config['QFW']['release'])
				return '';
			return "Ошибка подключения блока ".$Uri." адрес был разобран в\t\t ".
				$MCA['Path']."\n".$MCA['Error'];
		}

		$Params = func_get_args();
		array_shift($Params);
		if ($Params)
			$MCA['Params'] = array_merge($MCA['Params'], $Params);

		//Выставляем новые пути вызова и сохраняем старые
		list($lpPath, $this->ParentPath, $this->CurPath) =
			array($this->ParentPath, $this->CurPath, $MCA['Path']);

		$result = call_user_func_array(array($MCA['Class'], $MCA['Action'].$MCA['Type']), $MCA['Params']);

		//восстанавливаем пути вызова
		list($this->CurPath, $this->ParentPath) =
			array($this->ParentPath, $lpPath);

		//восстанавливаем MCA
		list ($this->cModule, $this->cController, $this->cAction) =
			array($oModule, $oController, $oAction);
		//Возвращаем путь к шаблонам после вызова
		QFW::$view->setScriptPath($scriptPath);

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
		//php_sapi_name если через nginx, то Status
		//а надо ли? - nginx и так понимает
		/*if (substr(PHP_SAPI, 0, 3) == 'cgi')
			header ('Status: 404 Not Found');
		else*/
		header((empty($_SERVER['SERVER_PROTOCOL']) ? 'HTTP/1.1 ' : $_SERVER['SERVER_PROTOCOL']).' 404 Not Found');
		if (!is_file(QFW::$view->getScriptPath().'/404.php'))
			QFW::$view->setScriptPath(APPPATH.'/default/templates/');
		die(QFW::$view->displayMain(QFW::$view->render('404.php')));
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
		$url = explode(self::PATH_SEPARATOR, $url);
		$n = min(3, count($url));
		if (isset($url[0]) && $url[0]==$this->defM && $n--)
			array_shift($url);
		if (count($url)>$n)
			return join(self::PATH_SEPARATOR, $url);
		$i = $n - 1;
		if (count($url)<=$n && isset($url[$i]) && $url[$i]==$this->defA)
		{
			unset($url[$i]);
			if (isset($url[0]) && $url[0]==$this->defC)
				array_shift($url);
		}
		if (count($url)==1 && $url[0]==$this->defC)
			array_shift($url);
		return join(self::PATH_SEPARATOR, $url);
	}

	/**
	 * Посылает заголовок Location для перехода на нужное действие
	 *
	 * @deprecated Используйте redirect(Url::...)
	 *
	 * @param string $MCA Uri нужного действия (модуль/контроллер/экшен)
	 * @param string|array $get GET парамерты или произвольный хвост
	 * @return exit
	 */
	public function redirectMCA($MCA, $get='')
	{
		trigger_error('Используйте redirect(Url::...)', E_USER_DEPRECATED);
		$base   = QFW::$config['redirection']['baseUrl'];
		$index  = QFW::$config['redirection']['useIndex']?'index.php/':'';
		$url    = QFW::$config['redirection']['useRewrite']?$this->backrewrite($MCA):$MCA;
		$url    = strtr($url, '/', self::PATH_SEPARATOR);
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
	 * <br>($_SERVER['REQUEST_URI'] по умолчанию)
	 * @param boolean $ref переходить по HTTP_REFERER, если есть, иначе на $url
	 * @return exit
	 */
	public function redirect($url='', $ref=false)
	{
		//если не указан - редирект откуда пришли
		if ($ref && isset($_SERVER['HTTP_REFERER']))
			$url = $_SERVER['HTTP_REFERER'];
		else
			$url = $url ? $url : Url::site($this->RequestUri);
		header('Location: '.$url);
		exit();
	}

	/** @var array Массив прямых преобразований Uri */
	protected $rewrite = false;
	/** @var array Массив обратных преобразований Uri */
	protected $backrewrite = false;
	/** @var array Массив обратных преобразований Url */
	protected $backrewriteUrl = false;
	
	/**
	 * Функция производит преобразования урла для вывода на страницу
	 *
	 * @internal
	 * @param string $uri Uri для бекреврайта
	 * @return string преобразованный Uri
	 */
	public function backrewrite($uri)
	{
		return $this->rewr($uri, 'backrewrite');
	}

	/**
	 * Функция производит финальные преобразования полного урла
	 *
	 * @internal
	 * @param string $url Url для бекреврайта
	 * @return string преобразованный Url
	 */
	public function backrewriteUrl($url)
	{
		return $this->rewr($url, 'backrewriteUrl');
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
		return $this->rewr($uri, 'rewrite');
	}

	/**
	 * Реализация преобразования адресов
	 *
	 * @internal
	 * @param string $uri Uri для реврайта
	 * @param string $type тип преобразования
	 * @return string преобразованный Uri
	 */
	private function rewr($uri, $type)
	{
		if (!QFW::$config['redirection']['useRewrite'])
			return $uri;
		if ($this->$type === false)
		{
			$rewrite = $backrewrite = $backrewriteUrl = array();
			require_once APPPATH . '/rewrite.php';
			$this->rewrite = $rewrite;
			$this->backrewrite = $backrewrite;
			$this->backrewriteUrl = $backrewriteUrl;
		}
		if (empty($this->$type))
			return $uri;
		if (is_array($this->$type))
			return preg_replace(array_keys($this->$type), array_values($this->$type), $uri);
		if (is_callable($this->$type))
		{
			$f = $this->$type;
			return $f($uri);
		}
		return $uri;
	}

	/**
	 * Парсит параметры, переданные как //p1/v1/p2/v2/p3
	 *
	 * @internal
	 */
	protected function parseParams(&$data)
	{
		if (empty($data) || $data[0]!='')
			return $data;
		trigger_error('Вы все еще кипятите? А мы уже рубим!', E_USER_DEPRECATED);
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
		while (isset($data[0]) AND $data[0] === '') array_shift($data);

		if (!empty(QFW::$config['cache']['MCA']))
		{
			$Cache = Cache::get('MCA');
			$key = 'MCA_'.crc32(serialize($data)).$type.
				($type=='Block' ? $this->curModule : $this->defM);
			$cached = $Cache->load($key);
			if ($cached)
			{
				$MCA = $cached['MCA'];
				//устанавливаем переменные роутера
				if ($this->module == '')
				{
					$this->module = $MCA['Module'];
					$this->controller = $MCA['Controller'];
					$this->action = $MCA['Action'];
					$this->type = $MCA['Type'];
				}
				$this->cModule = $MCA['Module'];
				$this->cController = $MCA['Controller'];
				$this->cAction = $MCA['Action'];
				//составляем путь и загружаем
				$path = $this->baseDir.'/'.$MCA['Module'];
				QFW::$view->setScriptPath($path.'/templates');
				$class = ucfirst($MCA['Controller']).'Controller';
				$fullname = $path . '/controllers/' . strtr($class,'_','/') . '.php';
				require_once($fullname);
				$class_key=$MCA['Module'].'|'.$MCA['Controller'];
				if (!isset($this->classes[$class_key]))
					$this->classes[$class_key] = array(
						'i'    => $MCA['Class'] = new $class,
						'defA' => $cached['defA'],
						'a'    => $cached['a'],
					);
				return $MCA;
			}
		}
		$MCA = array();

		//Определяем модуль
		if (isset($data[0]) && (is_dir($this->baseDir . '/' . $data[0])))
			$MCA['Module'] = array_shift($data);
		else
			$MCA['Module'] = $type=='Block' ? $this->curModule : $this->defM;
		$this->cModule = $MCA['Module'];
		if ($this->module == '') $this->module = $this->cModule;
		$path = $this->baseDir.'/'.$this->cModule;
		QFW::$view->setScriptPath($path.'/templates');

		$c=count($data); // Количество элементов URI исключая модуль
		//Определяем контроллер
		$cname = isset($data[0]) ? $data[0] : ($type=='Block' ? $this->curController : $this->defC);

		$class = ucfirst($cname).'Controller';
		$fullname = $path . '/controllers/' . strtr($class,'_','/') . '.php';

		if (is_file($fullname))
			array_shift($data);
		else
		{
			$cname = $type=='Block' ? $this->curController : $this->defC;
			$class = ucfirst($cname).'Controller';
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
		$this->cController = $MCA['Controller'];
		if ($this->controller == '') $this->controller = $this->cController;

		if (!isset($this->classes[$class_key]))
		{
			require_once($fullname);
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

			//Выполняется при первом вызове и сохраняет значение вызванного MCA
			//Проверяем последний так как остальные уже записаны
			if ($this->action == '')
			{
				$aname = isset($data[0]) ? $data[0] :
					(isset($vars['defA']) ? $vars['defA'] : $this->defA);
				if (!in_array(strtr($aname,'.','_').$type, $acts))
					$aname = (isset($vars['defA']) ? $vars['defA'] : $this->defA);
				$this->cAction = $this->action = $aname;
				$this->type = $type;
			}

			$this->classes[$class_key] = array(
				'i'    => new $class,
				'defA' => isset($vars['defA']) ? $vars['defA'] : $this->defA,
				'a'    => $acts,
			);
		}
		$MCA['Class'] = $this->classes[$class_key]['i'];
		
		$aname = isset($data[0]) ? $data[0] : $this->classes[$class_key]['defA'];
		$MCA['Action'] = strtr($aname,'.','_');
		$MCA['Type'] = $type;
		
		if (in_array($MCA['Action'].$MCA['Type'], $this->classes[$class_key]['a']))
			array_shift($data);
		else
		{
			$aname = $this->classes[$class_key]['defA'];
			$MCA['Action'] = strtr($aname,'.','_');
			$MCA['Type'] = $type;
			if (!in_array($aname.$type,$this->classes[$class_key]['a']))
			{
				$MCA['Error']="в классе \t\t\t".$class." \nне найдена функция \t\t".
				$MCA['Action'].$MCA['Type']."\nМетод не найден шоб его";
				$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/'.$aname;
				return $MCA;
			}
		}
		$this->cAction = $MCA['Action'];

		if (QFW::$config['QFW']['auto404'] && count($data)==$c && $c>0)
		{	 // если из URI после модуля ничего не забрали и что-то осталось
			$MCA['Error']="Указаны параметры у дефолтового CA \n".
				"или несуществующий Контроллер или Экшен дефолтового контроллера\n".
				"Не работает, мать его за ногу";
		}
		$MCA['Path']=$MCA['Module'].'/'.$MCA['Controller'].'/'.$aname;

		if (!empty(QFW::$config['cache']['MCA']))
			$Cache->save(array(
				'MCA' => $MCA,
				'defA' => $this->classes[$class_key]['defA'],
				'a' => $this->classes[$class_key]['a'],
			), $key, array());

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
		return trim($uri, self::PATH_SEPARATOR.'/');
	}

}
?>
