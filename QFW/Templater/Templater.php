<?php

abstract class Templater
{
	/** @var array переменные, установленные в шаблоне */
	protected $_vars;
	/** @var string путь к шаблонам */
	protected $_tmplPath;

	/** @var QuickFW_Plugs Плагины фреймворка */
	public $P;
	
	/** @var String Основной шаблон (путь относительно директории шаблонов) */
	public $mainTemplate;

	public function __construct($tmplPath, $mainTmpl)
	{
		$this->_vars = array();
		$this->_tmplPath = $tmplPath;
		$this->P = QuickFW_Plugs::getInstance();
		$this->mainTemplate = $mainTmpl;
	}

	/**
	* Assign variables to the template
	*
	* Allows setting a specific key to the specified value, OR passing an array
	* of key => value pairs to set en masse.
	*
	* @param string|array $spec The assignment strategy to use (key or array of key
	* => value pairs)
	* @param mixed $value (Optional) If assigning a named variable, use this
	* as the value.
	* @return Templater_PlainView
	*/
	public function assign($spec, $value = null)
	{
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
		if (is_array($spec))
			foreach ($spec as $item)
				unset($this->_vars[$item]);
		else
			unset($this->_vars[$spec]);
	}

	/**
	* Clear all assigned variables
	*
	* @return void
	*/
	public function clearVars()
	{
		$this->_vars=array();
	}

	/**
	 * Возвращает значения переменных в шаблоне
	 *
	 * @param string $var имя переменной
	 * @return mixed значение
	 */
	public function getTemplateVars($var = null)
	{
		if ($var === null)
			return $this->_vars;
		elseif (isset($this->_vars[$var]))
			return $this->_vars[$var];
		else
			return null;
	}

	/**
	 * Возвращает путь к шаблонам
	 *
	 * @return string путь
	 */
	public function getScriptPath()
	{
		return $this->_tmplPath;
	}

	/**
	 * Устанавливает путь к шаблонам
	 *
	 * @param string $path новый путь
	 * @return boolean корректный ли путь
	 */
	public function setScriptPath($path)
	{
		if (!is_readable($path))
			return false;
		$this->_tmplPath = $path;
		return true;
	}

	public function block($block)
	{
		//TODO: убрать ненужную переменную после перехода на php 5.3
		$args = func_get_args();
		return call_user_func_array(array(&QFW::$router, 'blockRoute'), $args);
	}

	public function render($tmpl)
	{
		return $this->fetch($tmpl);
	}

	abstract public function fetch($tmpl);

	public function displayMain($content)
	{
		if (isset($this->mainTemplate) && $this->mainTemplate!="")
		{
			//Необходимо для установки флага CSS
			$this->P->startDisplayMain();
			$this->assign('content',$content);
			$content = $this->render($this->mainTemplate);
		}
		//Необходимо для вызовов всех деструкторов
		QFW::$router->startDisplayMain();
		return $this->P->HeaderFilter($content);
	}

	/**
	 * Функции ескейпинга с учетом utf8
	 *
	 * @param string $s Исходная строка 
	 * @return string htmlspecialchars($s, ENT_QUOTES, 'UTF-8')
	 */
	public function esc($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');}

}

?>