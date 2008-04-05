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
	protected $IncFiles = array();
	
	public function addJS($params, &$smarty=null)
	{
		if (isset($params['file']))
			$this->IncFiles['js'][]=$params['file'];
		return "";
	}

	public function addCSS($params, &$smarty=null)
	{
		if (isset($params['file']))
			$this->IncFiles['css'][]=$params['file'];
		return "";
	}
	
	public function outHeader($params, &$smarty=null)
	{
		if (!isset($params['name']))
			$params['name']='default';
		return '<!--HEAD'.$params['name'].'-->';
	}
	
	public function getHeader($params, $content, &$smarty=null)
	{
		if ($content==null) return;
		if (!isset($params['name']))
			$params['name']='default';
			
		//TO DO:
		//загнать в массив и потом уникальные
		if (!isset($this->HeaderArr['<!--HEAD'.$params['name'].'-->']))
			$this->HeaderArr['<!--HEAD'.$params['name'].'-->']='';
		$this->HeaderArr['<!--HEAD'.$params['name'].'-->'].=$content;
	}
	
	public function HeaderFilter($text)
	{
		$head='';
		if (is_array($this->IncFiles['css']))
		{
			sort($this->IncFiles['css']);
			$this->IncFiles['css'] = array_unique($this->IncFiles['css']);
			foreach ($this->IncFiles['css'] as $v)
				$head.='<link rel="stylesheet" href="'.$v.'" type="text/css">'."\n";
		}
		if (is_array($this->IncFiles['js']))
		{
			sort($this->IncFiles['js']);
			$this->IncFiles['js'] = array_unique($this->IncFiles['js']);
			foreach ($this->IncFiles['js'] as $v)
				$head.='<script language="JavaScript" src="'.$v.'" type="text/javascript"></script>'."\n";
		}
		
		$text = str_replace('</head>',$head.'</head>',$text);
		$text = str_replace(array_keys($this->HeaderArr),array_values($this->HeaderArr),$text);
		return $text;
	}
	
}


?>