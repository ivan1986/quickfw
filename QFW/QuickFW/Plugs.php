<?php

class QuickFW_Plugs
{
	protected static $_thisInst = null;

	protected function __construct()
	{
	}

	public static function getInstance()
	{
		if (self::$_thisInst === null)
			self:: $_thisInst = new QuickFW_Plugs();
		return self::$_thisInst;
	}

	public function baseUrl()
	{
		trigger_error('Используйте Url::base()', E_USER_DEPRECATED);
		return QFW::$config['redirection']['baseUrl'];
	}

	public function siteDefUrl($url, $get='')
	{
		return $this->siteUrl(QFW::$router->delDef($url), $get);
	}

	/**
	 * @deprecated Используйте Url::...
	 */
	public function siteUrl($url, $get='')
	{
		trigger_error('Используйте Url::...', E_USER_DEPRECATED);
		if (QFW::$config['redirection']['delDef'])
			$url = QFW::$router->delDef($url);
		if (QFW::$config['redirection']['useRewrite'])
			$url = QFW::$router->backrewrite($url);
		if (is_array($get) && count($get))
			$get = '?'.http_build_query($get);
		return QFW::$config['redirection']['baseUrl'].
			(QFW::$config['redirection']['useIndex']?'index.php/':'').
			$url.
			($url!==''?QFW::$config['redirection']['defExt']:'').$get;
	}

	protected $Head = array();
	protected $HeadData = array();

	protected $IncFiles = array(
		'js_main'=>array(),
		'js'=>array(),
		'css_main'=>array(),
		'css'=>array(),
	);
	protected $isMain = false;

	public function startDisplayMain() { $this->isMain = true; }

	public function addJS($file, $noBase=false)
	{
		$this->IncFiles['js'.($this->isMain?'_main':'')][]=($noBase?'':QFW::$config['redirection']['baseUrl']).$file;
		return "";
	}

	public function addCSS($file, $noBase=false)
	{
		$this->IncFiles['css'.($this->isMain?'_main':'')][]=($noBase?'':QFW::$config['redirection']['baseUrl']).$file;
		return "";
	}

	//Cтандартные вставки - JS в начало и в конец документа и CSS в начало
	public function JSh($data) {return $this->getHead($data,'_JavaScript2HEAD',true);}
	public function JSe($data) {return $this->getHead($data,'_JavaScript2END',true);}
	public function CSS($data) {return $this->getHead($data,'_CSS2HEAD',true);}
	public function sJSh() {return $this->getHead(false,'_JavaScript2HEAD',true);}
	public function eJSh() {return $this->getHead(true ,'_JavaScript2HEAD',true);}
	public function sJSe() {return $this->getHead(false,'_JavaScript2END',true);}
	public function eJSe() {return $this->getHead(true ,'_JavaScript2END',true);}
	public function sCSS() {return $this->getHead(false,'_CSS2HEAD',true);}
	public function eCSS() {return $this->getHead(true ,'_CSS2HEAD',true);}

	//сокращения для JavaScript
	public function sJS($name='') {return $this->getHead(false,'_JavaScript_'.$name,true);}
	public function eJS($name='') {return $this->getHead(true ,'_JavaScript_'.$name,true);}
	public function oJS($name='') {return $this->outHead('_JavaScript_'.$name,"<script type=\"text/javascript\"><!--\n","\n--></script>\n");}

	public function outHead($name='default', $pre='',$post='')
	{
		$key = '<!--HEAD'.$name.'-->';
		if (array_key_exists($key,$this->Head))
			return '';
		$this->Head[$key]=array(
			'pre'=>$pre,
			'post'=>$post,
		);
		return $key;
	}

	public function getHead($content, $name='default', $join=false)
	{
		//для перехвата из PlainPHP
		if ($content===false)
		{
			ob_start();
			return;
		}
		if ($content===true)
		{
			$content=ob_get_contents();
			ob_end_clean();
		}

		$k = '<!--HEAD'.$name.'-->';

		if (!isset($this->HeadData[$k]))
			$this->HeadData[$k]='';
		if ($join)
			$this->HeadData[$k].=$content;
		else
			$this->HeadData[$k]=$content;
	}

	public function HeaderFilter($text)
	{
		$head='';
		$endSlash = QFW::$config['QFW']['addCSSXml'] ? '/' : '';
		$this->IncFiles['css'] = array_merge($this->IncFiles['css_main'], $this->IncFiles['css']);
		$this->IncFiles['js'] = array_merge($this->IncFiles['js_main'], $this->IncFiles['js']);

		$this->IncFiles['css'] = array_unique($this->IncFiles['css']);
		if (count($this->IncFiles['css'])>0)
			$head.='<link rel="stylesheet" href="'.
				join('" type="text/css" '.$endSlash.'>'."\n".'<link rel="stylesheet" href="', $this->IncFiles['css']).
				'" type="text/css" '.$endSlash.'>'."\n";

		$this->IncFiles['js'] = array_unique($this->IncFiles['js']);
		if (count($this->IncFiles['js'])>0)
			$head.='<script src="'.
				join('" type="text/javascript"></script>'."\n".'<script src="', $this->IncFiles['js']).
					'" type="text/javascript"></script>'."\n";

		foreach ($this->HeadData as $k=>$v)
		{
			if ($k=='<!--HEAD_JavaScript2HEAD-->')
				$head.="<script type=\"text/javascript\"><!--\n".$v."\n--></script>\n";
			elseif ($k=='<!--HEAD_JavaScript2END-->')
				$text = str_replace('</body>',"<script type=\"text/javascript\"><!--\n".$v."\n--></script>\n</body>",$text);
			elseif ($k=='<!--HEAD_CSS2HEAD-->')
				$head.="<style type=\"text/css\">\n".$v."\n</style>\n";
			elseif (!array_key_exists($k,$this->Head)) //если нету ключа, то добавляем вверх
				$head.=$v;
			elseif ($v!='') //если есть, то обрамляем pre и post и вставляем
			{
				$this->HeadData[$k]=$this->Head[$k]['pre'].$v.$this->Head[$k]['post'];
				continue; //оставляем элемент в массиве
			}
			unset($this->HeadData[$k]);
		}
		$head.="</head>\n";

		$text = str_replace('</head>',$head,$text);
		$text = str_replace(array_keys($this->HeadData),array_values($this->HeadData),$text);
		$text = preg_replace('|<!--HEAD.*?-->|','',$text);
		return $text;
	}

	/**
	 * Отображение сообщений об ошибках
	 */
	public function displayErrors($errors=array())
	{
		$res = '';
		if (!is_array($errors))
			return $res;
		foreach($errors as $error)
			$res .= $this->displayErrorsParams['pre'].$error.$this->displayErrorsParams['post'];
		return $res;
	}

	protected $displayErrorsParams = array('pre'=>'', 'post'=>'');

	/**
	 * Установка обрамления сообщений об ошибках
	 */
	public function setDisplayErrorsParams($pre='', $post='')
	{
		$this->displayErrorsParams = array('pre'=>$pre, 'post'=>$post);
	}

	/**
	 * Функции ескейпинга в нужной кодировке
	 *
	 * @param string $s Исходная строка
	 * @return string htmlspecialchars($s, ENT_QUOTES, $encoding)
	 */
	public function esc($s)
	{
		return htmlspecialchars($s, ENT_QUOTES,
			QFW::$config['host']['encoding']);
	}

}

?>