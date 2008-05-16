<?php

class QuickFW_Plugs
{
	protected static $_thisInst = null;
	protected $base, $defext, $index;
	
	protected function __construct()
	{
		global $config;
		$this->base=$config['redirection']['baseUrl'];

		$this->defext=$config['redirection']['defExt'];

		$this->index=$config['redirection']['useIndex'];
		$this->index=$this->index?'index.php/':'';
		
		$this->rewriter = $config['redirection']['useRewrite'];
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
	
	protected $HeaderArr = array();
	protected $HeaderOut = array();
	
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
	
	public function outHead($name='default')
	{
		$key = '<!--HEAD'.$name.'-->';
		if (!isset($this->HeaderOut[$key]))
			$this->HeaderOut[$key]=-1;
		$this->HeaderOut[$key]++;
		return $this->HeaderOut[$key]==0?$key:'';
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
		
		if (!isset($this->HeaderArr[$k]))
			$this->HeaderArr[$k]='';
		if ($join)
			$this->HeaderArr[$k].=$content;
		else 
			$this->HeaderArr[$k]=$content;
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
		
		foreach ($this->HeaderArr as $k=>$v)
		{
			if (!isset($this->HeaderOut[$k]))
			{
				$head.=$v;
				unset($this->HeaderArr[$k]);
			}
		}
		$head.="</head>\n";
		
		$text = str_replace('</head>',$head,$text);
		$text = str_replace(array_keys($this->HeaderArr),array_values($this->HeaderArr),$text);
		return $text;
	}
	
}


?>