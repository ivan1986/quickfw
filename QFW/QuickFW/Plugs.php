<?php

class QuickFW_Plugs
{
	protected static $_thisInst = null;
	protected $base, $defext, $index;

	protected $displayErrorsParams;

	protected function __construct()
	{
		global $config;
		$this->base=$config['redirection']['baseUrl'];

		$this->defext=$config['redirection']['defExt'];

		$this->index=$config['redirection']['useIndex'];
		$this->index=$this->index?'index.php/':'';

		$this->rewriter = $config['redirection']['useRewrite'];

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
		return $this->base;
	}

	public function siteUrl($url)
	{
		global $router;
		$url = $router->backrewrite($url);
		return $this->base.$this->index.$url.($url!==''?$this->defext:'');
	}

	protected $Head = array();
	protected $HeadData = array();

	protected $IncFiles = array();

	public function addJS($file, $noBase=false)
	{
		$this->IncFiles['js'][]=($noBase?'':$this->base).$file;
		return "";
	}

	public function addCSS($file, $noBase=false)
	{
		$this->IncFiles['css'][]=($noBase?'':$this->base).$file;
		return "";
	}

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
		$text = str_replace(array_keys($this->Head),array_values($this->HeadData),$text);
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