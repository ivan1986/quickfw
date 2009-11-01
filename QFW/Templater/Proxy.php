<?php

class Templater_Proxy
{
	/** @var QuickFW_Plugs Плагины фреймворка */
	public $P;
	public $mainTemplate;

	protected $_vars;
	protected $_tmplPath;

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
		$this->_vars = array();
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
		$this->unsyncronize();
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
		$this->unsyncronize();
		if (is_array($spec))
			$this->_vars = array_merge($this->_vars, $spec);
		else
			$this->_vars[$spec] = $value;
		return $this;
	}

	/**
	* Clear assigned variable
	*
	* @param string|array
	* @return void
	*/
	public function delete($spec)
	{
		$this->unsyncronize();
		if (is_array($spec))
			foreach ($spec as $item)
				$this->delete($item);
		elseif (isset($this->_vars[$spec]))
				unset($this->_vars[$spec]);
	}

	/**
	* Clear all assigned variables
	*
	* @return void
	*/
	public function clearVars()
	{
		$this->unsyncronize();
		$this->_vars=array();
	}

	public function getTemplateVars($var = null)
	{
		if ($var === null)
			return $this->_vars;
		elseif (isset($this->_vars[$var]))
			return $this->_vars[$var];
		else
			return null;
	}

	public function block($block)
	{
		return call_user_func_array(array(&QFW::$router, 'blockRoute'), func_get_args());
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
		$key=substr($name,strrpos($name,'.')+1);
		if (!array_key_exists($key, QFW::$config['templater']['exts']))
			return '';
		$T = QFW::$config['templater']['exts'][$key];

		if (!array_key_exists($T,$this->templates))
		{
			$templ = ucfirst($T);
			$class = 'Templater_'.$templ;
			require (QFWPATH.'/Templater/'.$templ.'.php');
			//Подключить класс шаблонизатора
			$this->templates[$T] = array(
				'c' => new $class(APPPATH,''),
				's' => true,
			);
			$this->syncronize($this->templates[$T]['c']);
		}
		elseif (!$this->templates[$T]['s'])
		{
			$this->syncronize($this->templates[$T]['c']);
			$this->templates[$T]['s']=true;
		}
		return $this->templates[$T]['c']->fetch($name);
	}

	protected function unsyncronize()
	{
		foreach ($this->templates as $k=>$v)
			$this->templates[$k]['s']=false;
	}

	protected function syncronize($tpl)
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
			//Необходимо для установки флага CSS
			$this->P->startDisplayMain();
			$this->assign('content',$content);
			$content = $this->fetch($this->mainTemplate);
		}
		//Необходимо для вызовов всех деструкторов
		QFW::$router->startDisplayMain();
		return $this->P->HeaderFilter($content);
	}

}
?>