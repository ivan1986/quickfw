<?php
/**
 * Loads and displays Kohana view files. Can also handle output of some binary
 * files, such as image, Javascript, and CSS files.
 *
 * $Id: View.php 4072 2009-03-13 17:20:38Z jheathco $
 *
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class View extends Templater {

	// The view file name and type
	protected $filename = FALSE;

	// View variable storage
	protected $kohana_local_data = array();
	protected static $kohana_global_data = array();

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
		{
			// Set the filename
			$this->set_filename($name);
		}

		if (is_array($data) AND ! empty($data))
		{
			// Preload data using array_merge, to allow user extensions
			$this->_vars = array_merge($this->_vars, $data);
		}
		$this->_tmplPath = QFW::$view->getScriptPath();
	}
	
	/**
	 * Magic method access to test for view property
	 *
	 * @param   string   View property to test for
	 * @return  boolean
	 */
	public function __isset($key = NULL)
	{
		return $this->is_set($key);
	}

	/**
	 * Sets the view filename.
	 *
	 * @chainable
	 * @param   string  view filename
	 * @param   string  view file type
	 * @return  object
	 */
	public function set_filename($name)
	{
		// Load the filename and set the content type
		$this->filename = $name;
		return $this;
	}

	/**
	 * Sets a view variable.
	 *
	 * @param   string|array  name of variable or an array of variables
	 * @param   mixed         value when using a named variable
	 * @return  object
	 */
	public function set($name, $value = NULL)
	{
		if (is_array($name))
		{
			foreach ($name as $key => $value)
			{
				$this->__set($key, $value);
			}
		}
		else
		{
			$this->__set($name, $value);
		}

		return $this;
	}

	/**
	 * Checks for a property existence in the view locally or globally. Unlike the built in __isset(), 
	 * this method can take an array of properties to test simultaneously.
	 *
	 * @param string $key property name to test for
	 * @param array $key array of property names to test for
	 * @return boolean property test result
	 * @return array associative array of keys and boolean test result
	 */
	public function is_set( $key = FALSE )
	{   //TODO: посмотреть
		// Setup result;
		$result = FALSE;

		// If key is an array
		if (is_array($key))
		{
			// Set the result to an array
			$result = array();
			
			// Foreach key
			foreach ($key as $property)
			{
				// Set the result to an associative array
				$result[$property] = (array_key_exists($property, $this->kohana_local_data) OR array_key_exists($property, View::$kohana_global_data)) ? TRUE : FALSE;
			}
		}
		else
		{
			// Otherwise just check one property
			$result = (array_key_exists($key, $this->kohana_local_data) OR array_key_exists($key, View::$kohana_global_data)) ? TRUE : FALSE;
		}

		// Return the result
		return $result;
	}

	/**
	 * Magically gets a view variable.
	 *
	 * @param  string  variable key
	 * @return mixed   variable value if the key is found
	 * @return void    if the key is not found
	 */
	public function &__get($key)
	{   //TODO: посмотреть
		if (isset($this->kohana_local_data[$key]))
			return $this->kohana_local_data[$key];

		if (isset(View::$kohana_global_data[$key]))
			return View::$kohana_global_data[$key];

		if (isset($this->$key))
			return $this->$key;
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
