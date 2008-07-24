<?php

class Templater_Templater
{
	/**
	* Плагины фреймворка - необходимо инициализировать
	*
	* @var QuickFW_Plugs
	*/
	public $P;

	/**
	* Основной шаблон (путь относительно директории шаблонов)
	*
	* @var String
	*/
	public $mainTemplate;


	/**
	* Constructor
	*
	* @param string $tmplPath - директория шаблонов
	* @param string $mainTpl - основной шаблон
	* @return void
	*/
	public function __construct($tmplPath, $mainTpl)
	{
		$this->mainTemplate = $mainTpl;
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
	}

	/**
	* Retrieve the current template directory
	*
	* @return string
	*/
	public function getScriptPath()
	{
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
	}

	/**
	* Clear assigned variable
	*
	* @param string|array
	* @return void
	*/
	public function delete($key)
	{
	}

	/**
	* Clear all assigned variables
	*
	* @return void
	*/
	public function clearVars()
	{
	}

	public function getTemplateVars($var = null)
	{
	}

	/**
	* Processes a template and returns the output.
	*
	* @param string $name The template to process.
	* @return string The output.
	*/
	public function render($name)
	{
	}

	public function fetch($name)
	{
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
		$content = $this->P->HeaderFilter($content);
		return $content;
	}

}
?>