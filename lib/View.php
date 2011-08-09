<?php
/**
 * Класс шаблона, представляющего собой переменную,
 * основан на шаблоне из Kohana
 *
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @author     Ivan Borzenkov <ivan1986@list.ru>
 */
class View extends Templater
{

	// The view file name and type
	protected $filename = FALSE;

	/**
	 * Creates a new View using the given parameters.
	 *
	 * @param   string  view name
	 * @param   array   pre-load data
	 * @param   string  type of file: html, css, js, etc.
	 * @return  object
	 */
	public static function factory($name = NULL, $data = NULL)
	{
		return new View($name, $data);
	}

	/**
	 * Attempts to load a view and pre-load view data.
	 *
	 * @throws  Kohana_Exception  if the requested view cannot be found
	 * @param   string  view name
	 * @param   array   pre-load data
	 * @param   string  type of file: html, css, js, etc.
	 * @return  void
	 */
	public function __construct($name = NULL, $data = NULL)
	{
		if (is_string($name) AND $name !== '')
			$this->set_filename($name);
		if (is_array($data) AND !empty($data))
			$this->_vars = array_merge($this->_vars, $data);
		$this->_tmplPath = QFW::$view->getScriptPath();
	}

	/**
	 * Sets the view filename.
	 *
	 * @chainable
	 * @param   string  view filename
	 * @return  object
	 */
	public function set_filename($name)
	{
		// Load the filename and set the content type
		$this->filename = $name;
		return $this;
	}

	/**
	 * Renders a view.
	 *
	 * @param   boolean   set to TRUE to echo the output instead of returning it
	 * @return  string    if print is FALSE
	 * @return  void      if print is TRUE
	 */
	public function fetch($tmpl=false, $vars=array())
	{
		$tmpl = $tmpl ? $tmpl : $this->filename;
		// Merge global and local data, local overrides global with the same name
		$data = array_merge($this->_vars, $vars);
		return QFW::$view->fetch($tmpl, $data);
	}

	/**
	 * Синоним fetch
	 */
	public function render($tmpl=false, $vars=array())
	{
		return $this->fetch($tmpl, $vars);
	}

	/**
	 * Magically converts view object to string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		try
		{
			return $this->fetch('', array());
		}
		catch (Exception $e)
		{
			// Display the exception using its internal __toString method
			return (string) $e;
		}
	}
}
