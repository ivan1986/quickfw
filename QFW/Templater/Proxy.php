<?php

class Templater_Proxy
{
	public $P;
	public $mainTemplate;

	protected $_vars;
	protected $_tmplPath;

	protected $sync;
	protected $templates;

	/**
	* Constructor
	*
	* @param string $tmplPath - директория шаблонов
	* @param string $mainTpl - основной шаблон
	* @return void
	*/
	public function __construct($tmplPath, $mainTpl)
	{
		$this->sync = false;
		$this->mainTemplate = $mainTpl;
		$this->P = QuickFW_Plugs::getInstance();
		$this->templates = array();
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
		$this->sync = false;
		$this->_tmplPath = $path;
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
		$this->sync = false;
		if (is_array($spec))
			$this->_vars = array_merge($this->_vars, $spec);
		else
			$this->_vars[$spec] = $value;
	}

	/**
	* Clear assigned variable
	*
	* @param string|array
	* @return void
	*/
	public function delete($key)
	{
		$this->sync = false;
		if (is_array($spec))
			foreach ($spec as $item)
				$this->delete($item);
		else
			if (isset($this->_vars[$spec]))
				unset($this->_vars[$spec]);
	}

	/**
	* Clear all assigned variables
	*
	* @return void
	*/
	public function clearVars()
	{
		$this->sync = false;
		$this->_vars=array();
	}

	public function getTemplateVars($var = null)
	{
		if ($var === null)
			return $this->_vars;
		else if (isset($this->_vars[$var]))
			return $this->_vars[$var];
		else
			return null;
	}

	public function module($module)
	{
		$result = '';
		QuickFW_Module::getTemplate($module, $result, $this);
		return $result;
	}

	/**
	* Processes a template and returns the output.
	*
	* @param string $name The template to process.
	* @return string The output.
	*/
	public function render($name)
	{
		return $this->fetch($name);
	}

	public function fetch($name)
	{
		global $config;
		foreach ($config['templater']['exts'] as $k=>$v)
		{
			if (strrpos($name,'.'.$k)+1+strlen($k)==strlen($name))
			{
				if (!array_key_exists($v,$this->templates))
				{
					$templ = ucfirst($v);
					$class = 'Templater_'.$templ;
					require (QFWPATH.'/Templater/'.$templ.'.php');
					//Подключить класс шаблонизатора
					$this->templates[$v] = array(
						'c' => new $class(ROOTPATH .'/application',''),
						's' => true,
					);
					$this->syncro($this->templates[$v]['c']);
				}
				if (!$this->templates[$v]['s'])
				{
					$this->syncro($this->templates[$v]['c']);
					$this->templates[$v]['s']=true;
				}
				$content=$this->templates[$v]['c']->fetch($name);
				return $content;
			}
		}
		return '';
	}

	protected function syncro($tpl)
	{
		$tpl->mainTemplate=$this->mainTemplate;
		$tpl->setScriptPath($this->_tmplPath);
		$tpl->clearVars();
		$tpl->assign($this->_vars);
	}

	/**
	* Выводит основной шаблон, обрабатывает функцией HeaderFilter
	*
	* @param string $name The template to process.
	* @return string The output.
	*/
	public function displayMain($content)
	{
		if (isset($this->mainTemplate) && $this->mainTemplate!="")
		{
			$this->assign('content',$content);
			$content = $this->fetch($this->mainTemplate);
		}
        return $content;
	}

}
?>