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
	
	public function Register4Smarty(&$smarty)
	{
		$smarty->register_function('outHead',array($this,'outHeader'));
		$smarty->register_block('getHead',array($this,'getHeader'));
		$smarty->register_outputfilter(array($this,'HeaderFilter'));
		
		$smarty->register_function('addJS',array($this,'addJS'));
		$smarty->register_function('addCSS',array($this,'addCSS'));
		
		$smarty->register_function('baseUrl',array($this,'baseUrl'));
		$smarty->register_function('siteUrl',array($this,'siteUrl'));
	}
	
	public function baseUrl($params, &$smarty=null)
	{
		return $this->base;
	}

	public function siteUrl($params, &$smarty=null)
	{
		global $router;
		if (!isset($params['url'])) return baseUrl();
		$url=$params['url'];
		$url = $router->backroute($url);
		return $this->base.$this->index.$url.$this->defext;
	}
	
	protected $HeaderArr = array();
	protected $HeaderOut = array();
	
	protected $IncFiles = array();
	
	public function addJS($params, &$smarty=null)
	{
		if (isset($params['file']))
			$this->IncFiles['js'][]=(isset($params['noBase'])?'':$this->base).$params['file'];
		return "";
	}

	public function addCSS($params, &$smarty=null)
	{
		if (isset($params['file']))
			$this->IncFiles['css'][]=(isset($params['noBase'])?'':$this->base).$params['file'];
		return "";
	}
	
	public function outHeader($params, &$smarty=null)
	{
		if (!isset($params['name']))
			$params['name']='default';
		$key = $params['name'];
		//$HeaderOut[$key]=1;
		var_dump($this->HeaderOut);
		if (!isset($this->HeaderOut[$key]))
			$this->HeaderOut[$key]=-1;
		$this->HeaderOut[$key]++;
		var_dump($this->HeaderOut);
		return $this->HeaderOut[$key]==0?'<!--HEAD'.$key.'-->':'';
	}
	
	public function getHeader($params, $content, &$smarty=null)
	{
		//для двух шаблонизаторов
		if ($content===null) return;
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

		if (!isset($params['name']))
			$params['name']='default';
			
		$key = '<!--HEAD'.$params['name'].'-->';
		if (!isset($this->HeaderArr[$key]))
			$this->HeaderArr[$key]='';
		if (isset($params['join']))
			$this->HeaderArr[$key].=$content;
		else 
			$this->HeaderArr[$key]=$content;
	}
	
	public function HeaderFilter($text)
	{
		$head='';
		if (count($this->IncFiles['css'])>0)
		{
			sort($this->IncFiles['css']);
			$this->IncFiles['css'] = array_unique($this->IncFiles['css']);
			$head.='<link rel="stylesheet" href="'.
				join('" type="text/css">'."\n".'<link rel="stylesheet" href="', $this->IncFiles['css']).
				'" type="text/css">'."\n";
		}
		if (count($this->IncFiles['js'])>0)
		{
			sort($this->IncFiles['js']);
			$this->IncFiles['js'] = array_unique($this->IncFiles['js']);
			$head.='<script language="JavaScript" src="'.
				join('" type="text/javascript"></script>'."\n".'<script language="JavaScript" src="', $this->IncFiles['js']).
				'" type="text/javascript"></script>'."\n";
		}
		
		$text = str_replace('</head>',$head.'</head>',$text);
		$text = str_replace(array_keys($this->HeaderArr),array_values($this->HeaderArr),$text);
		return $text;
	}
	
}


?>