<?php

class QuickFW_Smarty
{
	protected $_smarty;
	
	/**
	* Плагины фреймворка
	* 
	* @var QuickFW_Plugs
	*/
	public $P;
	
	protected $_tmplPath;
	/**
	* Template file in which is all content
	*
	* @var string
	*/
	public $mainTemplate;
	
	/**
	 * Init - подключение и инициальзация смарти - вынесено так как только по требованию
	 *
	 */
	protected function Init()
	{
		global $config;
		require QFWPATH.'/Smarty'.($config['release']?'':'.debug').'/Smarty.class.php';
		$this->_smarty = new Smarty;
		
		$this->_smarty->force_compile = !$config['release'];
	
		$this->_smarty->compile_dir = TMPPATH . '/templates_c';
		$this->_smarty->config_dir  = TMPPATH . '/configs';
		$this->_smarty->cache_dir   = TMPPATH . '/cache';
		
		if (null !== $this->_tmplPath) {
			$this->setScriptPath($this->_tmplPath);
		}
	
		require QFWPATH.'/QuickFW/Module.php';
		$module = QuickFW_Module::getInstance();
		$this->_smarty->register_resource('module', array($module,
													"getTemplate",
													"getTimestamp",
													"isSecure",
													"isTrusted"));
		$this->regPlugs();
	}
	
	/**
	* Constructor
	*
	* @param string $tmplPath
	* @param string $mainTpl
	* @return void
	*/
	public function __construct($tmplPath, $mainTpl)
	{
		$this->_tmplPath = $tmplPath;
		$this->mainTemplate = $mainTpl;

		require QFWPATH.'/QuickFW/Plugs.php';
		$this->P = QuickFW_Plugs::getInstance();
	}
	
	/**
	* Set the path to the templates
	*
	* @param string $path The directory to set as the path.
	* @return void
	*/
	public function setScriptPath($path)
	{
		if (!is_readable($path)) 
		return false;
		$this->_tmplPath = $path;
		if ($this->_smarty)
			$this->_smarty->template_dir = $path;
		return true;
	}
	
	/**
	* Retrieve the current template directory
	*
	* @return string
	*/
	public function getScriptPath()
	{
		return $this->_tmplPath;
	}
	
	/**
	* Assign variables to the template
	*
	* Allows setting a specific key to the specified value, OR passing an array
	* of key => value pairs to set en masse.
	*
	* @see __set()
	* @param string|array $spec The assignment strategy to use (key or array of key
	* => value pairs)
	* @param mixed $value (Optional) If assigning a named variable, use this
	* as the value.
	* @return void
	*/
	public function assign($spec, $value = null)
	{
		if (is_array($spec))
		{
			$this->getEngine()->assign($spec);
			return;
		}
		
		$this->getEngine()->assign($spec, $value);
	}
	
	/**
	* Clear assigned variable
	*
	* @param string|array
	* @return void
	*/
	public function delete($key)
	{
		$this->getEngine()->clear_assign($key);
	}
	
	/**
	* Clear all assigned variables
	*
	* @return void
	*/
	public function clearVars()
	{
		$this->getEngine()->clear_all_assign();
	}
	
	public function getTemplateVars($var = null)
	{
		if ($var === null)
			return $this->getEngine()->get_template_vars();
		return $this->getEngine()->get_template_vars($var);
	}
    
	/**
	* Processes a template and returns the output.
	*
	* @param string $name The template to process.
	* @return string The output.
	*/
	public function render($name)
	{
		return $this->getEngine()->fetch($name);
	}

	public function fetch($name)
	{
		return $this->getEngine()->fetch($name);
	}
	
	public function displayMain()
	{
		if (isset($this->mainTemplate) && $this->mainTemplate!="")
			$content = $this->getEngine()->fetch($this->mainTemplate);
		else
			$content = $this->getEngine()->get_template_vars('content');
        $content = $this->P->HeaderFilter($content);
        echo $content;
	}
	
	/**
	* Return the template engine object
	*
	* @return Smarty
	*/
	private function getEngine()
	{
		if (!$this->_smarty)
		{
			$this->Init();
		}
		return $this->_smarty;
	}
	
	//Plugins Wrapper
	protected function regPlugs()
	{
		$this->_smarty->register_function('baseUrl',array($this,'s_baseUrl'));
		$this->_smarty->register_function('siteUrl',array($this,'s_siteUrl'));
		$this->_smarty->register_modifier('siteUrl',array($this,'m_siteUrl'));
		
		$this->_smarty->register_function('addJS',array($this,'s_addJS'));
		$this->_smarty->register_function('addCSS',array($this,'s_addCSS'));
		
		$this->_smarty->register_function('outHead',array($this,'s_outHead'));
		$this->_smarty->register_block('getHead',array($this,'s_getHead'));

		$this->_smarty->register_function('pluralForm',array($this,'s_pluralForm'));
	}
	
	public function s_pluralForm($params, &$smarty)
	{
		$sep=isset($params['sep'])?$params['sep']:',';
		$forms=explode($sep,$params['forms'],3);
		return $this->P->pluralForm($params['num'],$forms[0],$forms[1],$forms[2]);
	}
	
	public function s_baseUrl($params, &$smarty)
		{return $this->P->baseUrl();}
	public function s_siteUrl($params, &$smarty) 
		{return !isset($params['url'])?$this->P->baseUrl():$this->P->siteUrl($params['url']);}
	public function m_siteUrl($url)
		{return $this->P->siteUrl($url);}
	public function s_addJS($params, &$smarty)
	{
		if (isset($params['file']))
			$this->P->addJS($params['file'],isset($params['noBase']));
		return "";
	}
	public function s_addCSS($params, &$smarty)
	{
		if (isset($params['file']))
			$this->P->addCSS($params['file'],isset($params['noBase']));
		return "";
	}
	public function s_outHead($params, &$smarty) 
	{
		return $this->P->outHead(isset($params['name'])?$params['name']:'default');
	}
	public function s_getHead($params, $content, &$smarty) 
	{
		if ($content===null)
			return;
		return $this->P->getHead($content, isset($params['name'])?$params['name']:'default', isset($params['join']));
	}
	
}
?>