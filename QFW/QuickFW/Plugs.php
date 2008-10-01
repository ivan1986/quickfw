<?php

class QuickFW_Plugs
{
	protected static $_thisInst = null;

	protected $displayErrorsParams;

	protected function __construct()
	{
		$this->setDisplayErrorsParams();
	}

	public static function getInstance()
	{
		if (self::$_thisInst === null)
		{
			self:: $_thisInst = new QuickFW_Plugs();
		}
		return self::$_thisInst;
	}

	public function baseUrl()
	{
		return QFW::$config['redirection']['baseUrl'];
	}

	public function siteUrl($url,$get='')
	{
		global $router;
		if (QFW::$config['redirection']['useRewrite'])
			$url = $router->backrewrite($url);
		if (is_array($get))
		{
			foreach($get as $k=>$v)
				$get[$k]=$k.'='.$v;
			$get='?'.implode('&',$get);
		}
		return QFW::$config['redirection']['baseUrl'].
				(QFW::$config['redirection']['useIndex']?'index.php/':'').
				$url.
				($url!==''?QFW::$config['redirection']['defExt']:'').$get;
	}

	protected $Head = array();
	protected $HeadData = array();

	protected $IncFiles = array();

	public function addJS($file, $noBase=false)
	{
		$this->IncFiles['js'][]=($noBase?'':QFW::$config['redirection']['baseUrl']).$file;
		return "";
	}

	public function addCSS($file, $noBase=false)
	{
		$this->IncFiles['css'][]=($noBase?'':QFW::$config['redirection']['baseUrl']).$file;
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

	public function pluralForm($n, $form1, $form2, $form5)
	{
		$n = abs($n) % 100;
		$n1 = $n % 10;
		if ($n > 10 && $n < 20) return $form5;
		if ($n1 > 1 && $n1 < 5) return $form2;
		if ($n1 == 1) return $form1;
		return $form5;
	}

	public function HeaderFilter($text)
	{
		$head='';
		if (isset($this->IncFiles['css']))
		{
			sort($this->IncFiles['css']);
			$this->IncFiles['css'] = array_unique($this->IncFiles['css']);
			$head.='<link rel="stylesheet" href="'.
				join('" type="text/css" />'."\n".'<link rel="stylesheet" href="', $this->IncFiles['css']).
				'" type="text/css" />'."\n";
		}
		if (isset($this->IncFiles['js']))
		{
			sort($this->IncFiles['js']);
			$this->IncFiles['js'] = array_unique($this->IncFiles['js']);
			$head.='<script src="'.
				join('" type="text/javascript"></script>'."\n".'<script src="', $this->IncFiles['js']).
				'" type="text/javascript"></script>'."\n";
		}

		foreach ($this->HeadData as $k=>$v)
		{
			if ($k=='<!--HEAD_JavaScript2HEAD-->')
			{
				$head.="<script type=\"text/javascript\"><!--\n".$v."\n--></script>\n";
				unset($this->HeadData[$k]);
				continue;
			}
			if ($k=='<!--HEAD_JavaScript2END-->')
			{
				$text = str_replace('</body>',"<script type=\"text/javascript\"><!--\n".$v."\n--></script>\n</body>",$text);
				unset($this->HeadData[$k]);
				continue;
			}
			if ($k=='<!--HEAD_CSS2HEAD-->')
			{
				$head.="<style type=\"text/css\">\n".$v."\n</style>\n";
				unset($this->HeadData[$k]);
				continue;
			}
			if (!array_key_exists($k,$this->Head))
			{	//если нету ключа, то добавляем вверх
				$head.=$v;
				unset($this->HeadData[$k]);
			}
			else
			{	//если есть, то обрамляем pre и post и вставляем
				if ($v!='')
					$this->HeadData[$k]=$this->Head[$k]['pre'].$v.$this->Head[$k]['post'];
			}
		}
		$head.="</head>\n";

		$text = str_replace('</head>',$head,$text);
		$text = str_replace(array_keys($this->HeadData),array_values($this->HeadData),$text);
		$text = preg_replace('|<!--HEAD.*?-->|','',$text);
		return $text;
	}

	public function displayErrors($errors)
	{
		$res = '';
		if (isset($errors)) {
			foreach($errors as $error){
				$res .= $this->displayErrorsParams['pre'].$error.$this->displayErrorsParams['post'];
			 }
		}
		return $res;
	}

	public function setDisplayErrorsParams($pre='', $post='')
	{
		$this->displayErrorsParams = array('pre'=>$pre, 'post'=>$post);
	}
}

?>