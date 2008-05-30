<?php

class True_Validation {

	var $_rules				= array(); //Правила для проверки
	var $_data				= array(); //Данные для проверки.
	var $_fields			= array(); //Заголовки полей
	var $_errors			= array(); //Массив ошибок
	var $_error_messages	= array();//Собственные сообщения об ошибках

	function True_Validation() {
		$this->init();
		require_once(LIBPATH.'/Language.php');
	}

	function init() {
		$_rules = array();
		$_data = array();
		$_fields = array();
	}

	function set_data($data = '') {
		if ($data == '') {
			return FALSE;
		} else {
			$this->_data = $data;
		}
	}

	function set_rules($rules = '') {
		if ($rules == '') {
			return;
		}

		foreach ($rules as $key => $val) {
			$this->_rules[$key] = $val;
		}
	}

	function set_fields($data = '', $field = '') {
		if ($data == '') {
			if (count($this->_fields) == 0) {
				return FALSE;
			}
		} else {
			if ( ! is_array($data)) {
				$data = array($data => $field);
			}

			if (count($data) > 0) {
				$this->_fields = $data;
			}
		}
	}

	function set_message($lang, $val = '') {
		if ( ! is_array($lang))
		{
			$lang = array($lang => $val);
		}

		$this->_error_messages = array_merge($this->_error_messages, $lang);
	}

	function add_error($field, $error) {
		$this->_errors[$field][] = $error;
	}

	// --------------------------------------------------------------------

	/**
	 * Run the Validator
	 *
	 * This function does all the work.
	 *
	 * @access	public
	 * @return	bool
	 */
	function run($clean_errors = TRUE) {
		if ($clean_errors) {
			$this->_errors = array();
		}

		// Do we even have any data to process?  Mm?
		if (count($this->_data) == 0 OR count($this->_rules) == 0) {
			return FALSE;
		}

		// Load the language file containing error messages
		QFW::$libs['lang']->load('true_validation');

		// Cycle through the rules and test for errors
		foreach ($this->_rules as $field => $rules) {
			//Explode out the rules!
			$ex = explode('|', $rules);

			/*
			 * Are we dealing with an "isset" rule?
			 *
			 * Before going further, we'll see if one of the rules
			 * is to check whether the item is set (typically this
			 * applies only to checkboxes).  If so, we'll
			 * test for it here since there's not reason to go
			 * further
			 */
			if ( ! isset($this->_data[$field])) {
				if (in_array('isset', $ex, TRUE) OR in_array('required', $ex)) {
					if ( ! isset($this->_error_messages['isset'])) {
						if (FALSE === ($line = QFW::$libs['lang']->line('isset'))) {
							$line = 'The field was not set';
						}
					} else {
						$line = $this->_error_messages['isset'];
					}

					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$this->add_error($field, sprintf($line, $mfield));
				}

				continue;
			}

			/*
			 * Set the current field
			 *
			 * The various prepping functions need to know the
			 * current field name so they can do this:
			 *
			 * $this->data[$this->_current_field] == 'bla bla';
			 *
			$this->_current_field = $field;*/

			// Cycle through the rules!
			foreach ($ex As $rule) {
				// Strip the parameter (if exists) from the rule
				// Rules can contain a parameter: max_length[5]
				$param = FALSE;
				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
					$rule	= $match[1];
					$param	= $match[2];
				}

				// Call the function that corresponds to the rule
				if ( ! method_exists($this, $rule)) {
					/*
					 * Run the native PHP function if called for
					 *
					 * If our own wrapper function doesn't exist we see
					 * if a native PHP function does. Users can use
					 * any native PHP function call that has one param.
					 */
					if (function_exists($rule)) {
						$this->_data[$field] = $rule($this->_data[$field]);
//						$this->$field = $this->data[$field];
					}

					continue;
				}

				$result = $this->$rule($this->_data[$field], $param);

				// Did the rule test negatively?  If so, grab the error.
				if ($result === FALSE) {
					if ( ! isset($this->_error_messages[$rule])) {
						if (FALSE === ($line = QFW::$libs['lang']->line($rule))) {
							$line = 'Unable to access an error message corresponding to your field name.';
						}
					} else {
						$line = $this->_error_messages[$rule];
					}

					// Build the error message
					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$mparam = ( ! isset($this->_fields[$param])) ? $param : $this->_fields[$param];
					// Add the error to the error array
					$this->add_error($field, sprintf($line, $mfield, $mparam));

					continue 2;
				}
			}

		}

		$total_errors = count($this->_errors);

		/*
		 * Recompile the class variables
		 *
		 * If any prepping functions were called the $this->data data
		 * might now be different then the corresponding class
		 * variables so we'll set them anew.
		 */
		if ($total_errors > 0) {
			$this->_safe_form_data = TRUE;
		}

//!!!		$this->set_fields();

		// Did we end up with any errors?
		if ($total_errors == 0) {
			return TRUE;
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function matches($str, $field)
	{
		if ( ! isset($this->_data[$field]))
		{
			return FALSE;
		}

		return ($str !== $this->_data[$field]) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function min_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function max_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function exact_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate IP Address
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function valid_ip($ip)
	{
		return true;//!!!!!$this->CI->valid_ip($ip);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	int
	 * @return	bool
	 */
	function numeric($str)
	{
		return ( ! ereg("^[0-9\.]+$", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Login
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function login($str)
	{
		return ( ! ereg("^[a-z][0-9a-z_]+[0-9a-z]$", $str)) ? FALSE : TRUE;
	}


	// --------------------------------------------------------------------

	/**
	 * Passport login (can include @ and domain after it)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function passport_login($str)
	{
		return ( ! preg_match("/^[0-9a-z_\-][0-9a-z_\-\.]+(@([0-9a-z][0-9a-z\-]*[0-9a-z]\.)+[0-9a-z]{2,6})?$/i", $str)) ? FALSE : TRUE;
	}

	function valid_url($str) {
		return ( !preg_match('/\b((ht|f)tp):(\/\/)([a-z0-9.:@*()~#\]\[_?=&\/\\-])+/', $str)) ? FALSE : TRUE;
	}

	function range($str, $val) {
		$val = explode('-', $val);
		if ((count($val)!==2) || preg_match("/[^0-9]/", $val[0]) || preg_match("/[^0-9]/", $val[1])) {
			return FALSE;
		}
		return (($tmp = intval($str) < $val[0]) || ($tmp > $val[1])) ? FALSE : TRUE;
	}

}
// END Validation Class
QFW::$libs['true_validation'] = new True_Validation();
?>