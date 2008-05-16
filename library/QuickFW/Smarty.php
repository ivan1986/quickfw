<?php

class QuickFW_Smarty
{
	/**
	* Smarty object
	* @var Smarty
	*/
	protected $_smarty;
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
		require LIBPATH.'/Smarty'.($config['state']['release']?'':'.debug').'/Smarty.class.php';
		$this->_smarty = new Smarty;
		
		$this->_smarty->force_compile = !$config['state']['release'];
	
		$this->_smarty->compile_dir = TMPPATH . '/templates_c';
		$this->_smarty->config_dir  = TMPPATH . '/configs';
		$this->_smarty->cache_dir   = TMPPATH . '/cache';
		
		if (null !== $this->_tmplPath) {
			$this->setScriptPath($this->_tmplPath);
		}
	
		require LIBPATH.'/QuickFW/Module.php';
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
	* @param array $extraParams
	* @return void
	*/
	public function __construct($tmplPath = null, $mainTpl = 'main.tpl')
	{
		$this->_tmplPath = $tmplPath;
		$this->mainTemplate = $mainTpl;

		require LIBPATH.'/QuickFW/Plugs.php';
		$this->P = QuickFW_Plugs::getInstance();
	}
	
	/**
	* Return the template engine object
	*
	* @return Smarty
	*/
	public function getEngine()
	{
		if (!$this->_smarty)
		{
			$this->Init();
		}
		return $this->_smarty;
	}
	
	/**
	* Set the path to the templates
	*
	* @param string $path The directory to set as the path.
	* @return void
	*/
	public function setScriptPath($path)
	{
		if (is_readable($path)) {
			$this->_tmplPath = $path;
			if ($this->_smarty)
				$this->_smarty->template_dir = $path;
			return true;
		}
		return false;
	}
	
	/**
	* Retrieve the current template directory
	*
	* @return string
	*/
	public function getScriptPaths()
	{
		return $this->_tmplPath;
	}
	
	/**
	* Alias for setScriptPath
	*
	* @param string $path
	* @param string $prefix Unused
	* @return void
	*/
	public function setBasePath($path, $prefix = 'Zend_View')
	{
		return $this->setScriptPath($path);
	}
	
	/**
	* Alias for setScriptPath
	*
	* @param string $path
	* @param string $prefix Unused
	* @return void
	*/
	public function addBasePath($path, $prefix = 'Zend_View')
	{
		return $this->setScriptPath($path);
	}
	
	/**
	* Assign a variable to the template
	*
	* @param string $key The variable name.
	* @param mixed $val The variable value.
	* @return void
	*/
	public function __set($key, $val)
	{
		$this->getEngine()->assign($key, $val);
	}
	
	/**
	* Retrieve an assigned variable
	*
	* @param string $key The variable name.
	* @return mixed The variable value.
	*/
	public function __get($key)
	{
		return $this->getEngine()->get_template_vars($key);
	}
	
	/**
	* Allows testing with empty() and isset() to work
	*
	* @param string $key
	* @return boolean
	*/
	public function __isset($key)
	{
		return (null !== $this->getEngine()->get_template_vars($key));
	}
	
	/**
	* Allows unset() on object properties to work
	*
	* @param string $key
	* @return void
	*/
	public function __unset($key)
	{
		$this->getEngine()->clear_assign($key);
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
	* Clear all assigned variables
	*
	* @return void
	*/
	public function clearVars()
	{
		$this->getEngine()->clear_all_assign();
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
	
	/*public function display($tpl)
	{
		echo $this->getEngine()->display($tpl);
	}*/
	
	public function displayMain()
	{
		if (isset($this->mainTemplate) && $this->mainTemplate!="")
			$content = $this->getEngine()->fetch($this->mainTemplate);
		else
			$content = $this->getEngine()->get_template_vars('content');
        $content = $this->P->HeaderFilter($content);
        echo $content;
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