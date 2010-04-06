<?php
/**
 * JsHttpRequest: PHP backend for JavaScript DHTML loader.
 * (C) Dmitry Koterov, http://en.dklab.ru
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * See http://www.gnu.org/copyleft/lesser.html
 *
 * Do not remove this comment if you want to use the script!
 * Не удаляйте данный комментарий, если вы хотите использовать скрипт!
 *
 * This backend library also supports POST requests additionally to GET.
 *
 * @author Dmitry Koterov
 * @version 5.x $Id$
 */

class JsHttpRequest
{
	var $SCRIPT_ENCODING = "utf-8";
	var $LOADER = null;
	var $ID = null;
	var $RESULT = null;

	// Internal; uniq value.
	private $_uniqHash;
	// Magic number for display_error checking.
	private $_magic = 14623;
	// Previous display_errors value.
	private $_prevDisplayErrors = null;
	// Internal: response content-type depending on loader type.
	private $_contentTypes = array(
		"script" => "text/javascript",
		"xml"    => "text/plain", // In XMLHttpRequest mode we must return text/plain - stupid Opera 8.0. :(
		"form"   => "text/html",
		""       => "text/plain", // for unknown loader
	);
	// Internal: conversion to UTF-8 JSON cancelled because of non-ascii key.
	private $_toUtfFailed = false;
	// Internal: list of characters 128...255 (for strpbrk() ASCII check).
	private $_nonAsciiChars = '';
	// Which Unicode conversion function is available?
	private $_unicodeConvMethod = null;
	// Emergency memory buffer to be freed on memory_limit error.
	private $_emergBuffer = null;


	/**
	 * Constructor.
	 *
	 * Create new JsHttpRequest backend object and attach it
	 * to script output buffer. As a result - script will always return
	 * correct JavaScript code, even in case of fatal errors.
	 *
	 * QUERY_STRING is in form of: PHPSESSID=<sid>&a=aaa&b=bbb&JsHttpRequest=<id>-<loader>
	 * where <id> is a request ID, <loader> is a loader name, <sid> - a session ID (if present),
	 * PHPSESSID - session parameter name (by default = "PHPSESSID").
	 *
	 * If an object is created WITHOUT an active AJAX query, it is simply marked as
	 * non-active. Use statuc method isActive() to check.
	 */
	function JsHttpRequest($enc)
	{
		global $JsHttpRequest_Active;

		// To be on a safe side - do not allow to drop reference counter on ob processing.
		$GLOBALS['_RESULT'] =& $this->RESULT;

		// Parse QUERY_STRING.
		if (preg_match('/^(.*)(?:&|^)JsHttpRequest=(?:(\d+)-)?([^&]+)((?:&|$).*)$/s', @$_SERVER['QUERY_STRING'], $m)) {
			$this->ID = $m[2];
			$this->LOADER = strtolower($m[3]);
			$_SERVER['QUERY_STRING'] = preg_replace('/^&+|&+$/s', '', preg_replace('/(^|&)'.session_name().'=[^&]*&?/s', '&', $m[1] . $m[4]));
			unset(
				$_GET['JsHttpRequest'],
				$_REQUEST['JsHttpRequest']
			);
			// Detect Unicode conversion method.
			$this->_unicodeConvMethod = function_exists('mb_convert_encoding')? 'mb' : (function_exists('iconv')? 'iconv' : 
				trigger_error('Install mb_string or iconv', E_USER_ERROR));

			// Fill an emergency buffer. We erase it at the first line of OB processor
			// to free some memory. This memory may be used on memory_limit error.
			$this->_emergBuffer = str_repeat('a', 1024 * 200);

			// Intercept fatal errors via display_errors (seems it is the only way).
			$this->_uniqHash = md5('JsHttpRequest' . microtime() . getmypid());
			$this->_prevDisplayErrors = ini_get('display_errors');
			ini_set('display_errors', $this->_magic); //
			ini_set('error_prepend_string', $this->_uniqHash . ini_get('error_prepend_string'));
			ini_set('error_append_string',  ini_get('error_append_string') . $this->_uniqHash);
			if (function_exists('xdebug_disable')) xdebug_disable(); // else Fatal errors are not catched

			// Start OB handling early.
			ob_start(array(&$this, "_obHandler"));
			$JsHttpRequest_Active = true;

			// Set up the encoding.
			$this->setEncoding($enc);

			// Check if headers are already sent (see Content-Type library usage).
			// If true - generate a debug message and exit.
			$file = $line = null;
			if (headers_sent($file, $line)) {
				trigger_error(
					"HTTP headers are already sent" . ($line !== null? " in $file on line $line" : " somewhere in the script") . ". "
					. "Possibly you have an extra space (or a newline) before the first line of the script or any library. "
					. "Please note that JsHttpRequest uses its own Content-Type header and fails if "
					. "this header cannot be set. See header() function documentation for more details",
					E_USER_ERROR
				);
				exit();
			}
		} else {
			$this->ID = 0;
			$this->LOADER = 'unknown';
			$JsHttpRequest_Active = false;
		}
	}


	/**
	 * Static function.
	 * Returns true if JsHttpRequest output processor is currently active.
	 *
	 * @return boolean    True if the library is active, false otherwise.
	 */
	static function isActive()
	{
		return !empty($GLOBALS['JsHttpRequest_Active']);
	}


	/**
	 * void setEncoding(string $encoding)
	 *
	 * Set an active script encoding & correct QUERY_STRING according to it.
	 * Examples:
	 *   "windows-1251"          - set plain encoding (non-windows characters,
	 *                             e.g. hieroglyphs, are totally ignored)
	 */
	function setEncoding($enc)
	{
		$this->SCRIPT_ENCODING = strtolower($enc);
		$this->_correctSuperglobals();
	}


	/**
	 * Convert a PHP scalar, array or hash to JS scalar/array/hash. This function is
	 * an analog of json_encode(), but it can work with a non-UTF8 input and does not
	 * analyze the passed data. Output format must be fully JSON compatible.
	 *
	 * @param mixed $a   Any structure to convert to JS.
	 * @return string    JavaScript equivalent structure.
	 */
	private function php2js($a=false)
	{
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a)) {
			if (is_float($a)) {
				// Always use "." for floats.
				$a = str_replace(",", ".", strval($a));
			}
			// All scalars are converted to strings to avoid indeterminism.
			// PHP's "1" and 1 are equal for all PHP operators, but
			// JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
			// we should get the same result in the JS frontend (string).
			// Character replacements for JSON.
			static $jsonReplaces = array(
				array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
				array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')
			);
			return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
			if (key($a) !== $i) {
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList) {
			foreach ($a as $v) {
				$result[] = JsHttpRequest::php2js($v);
			}
			return '[ ' . join(', ', $result) . ' ]';
		} else {
			foreach ($a as $k => $v) {
				$result[] = JsHttpRequest::php2js($k) . ': ' . JsHttpRequest::php2js($v);
			}
			return '{ ' . join(', ', $result) . ' }';
		}
	}


	/**
	 * Internal methods.
	 */

	/**
	 * Parse & decode QUERY_STRING.
	 */
	private function _correctSuperglobals()
	{
		// In case of FORM loader we may go to nirvana, everything is already parsed by PHP.
		if ($this->LOADER == 'form') return;

		// ATTENTION!!!
		// HTTP_RAW_POST_DATA is only accessible when Content-Type of POST request
		// is NOT default "application/x-www-form-urlencoded"!!!
		// Library frontend sets "application/octet-stream" for that purpose,
		// see JavaScript code. In PHP 5.2.2.HTTP_RAW_POST_DATA is not set sometimes;
		// in such cases - read the POST data manually from the STDIN stream.
		$rawPost = strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0? (isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : @file_get_contents("php://input")) : null;
		$source = array(
			'_GET' => !empty($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING'] : null,
			'_POST'=> $rawPost,
		);
		foreach ($source as $dst=>$src) {
			// First correct all 2-byte entities.
			$s = preg_replace('/%(?!5B)(?!5D)([0-9a-f]{2})/si', '%u00\\1', $src);
			// Now we can use standard parse_str() with no worry!
			$data = null;
			parse_str($s, $data);
			$GLOBALS[$dst] = $this->_ucs2EntitiesDecode($data);
		}
		$_REQUEST =
			(isset($_COOKIE)? $_COOKIE : array()) +
			(isset($_POST)? $_POST : array()) +
			(isset($_GET)? $_GET : array());
	}


	/**
	 * Called in case of error too!
	 */
	function _obHandler($text)
	{
		unset($this->_emergBuffer); // free a piece of memory for memory_limit error
		unset($GLOBALS['JsHttpRequest_Active']);

		// Check for error & fetch a resulting data.
		$wasFatalError = false;
		if (preg_match_all("/{$this->_uniqHash}(.*?){$this->_uniqHash}/sx", $text, $m)) {
			// Display_errors:
			// 1. disabled manually after the library initialization, or
			// 2. was initially disabled and is not changed
			$needRemoveErrorMessages = !ini_get('display_errors') || (!$this->_prevDisplayErrors && ini_get('display_errors') == $this->_magic);
			foreach ($m[0] as $error) {
				if (preg_match('/\bFatal error(<.*?>)?:/i', $error)) {
					$wasFatalError = true;
				}
				if ($needRemoveErrorMessages) {
					$text = str_replace($error, '', $text); // strip the whole error message
				} else {
					$text = str_replace($this->_uniqHash, '', $text);
				}
			}
		}
		if ($wasFatalError) {
			// On fatal errors - force "null" result. This is needed, because $_RESULT
			// may not be fully completed at the moment of the error.
			$this->RESULT = null;
		} else {
			// Read the result from globals if not set directly.
			if (!isset($this->RESULT)) {
				global $_RESULT;
				$this->RESULT = $_RESULT;
			}
			// Avoid manual NULLs in the result (very important!).
			if ($this->RESULT === null) {
				$this->RESULT = false;
			}
		}

		// Note that 500 error is generated when a PHP error occurred.
		$status = $this->RESULT === null? 500 : 200;
		$result = array(
			'id'   => $this->ID,
			'js'   => $this->RESULT,  // null always means a fatal error...
			'text' => $text,          // ...independent on $text!!!
		);
		$encoding = $this->SCRIPT_ENCODING;
		$text = null; // to be on a safe side

		// Try to use very fast json_encode: 3-4 times faster than a manual encoding.
		if (function_exists('array_walk_recursive') && function_exists('json_encode')) {
			$this->_nonAsciiChars = join("", array_map('chr', range(128, 255)));
			$this->_toUtfFailed = false;
			$resultUtf8 = $result;
			array_walk_recursive($resultUtf8, array(&$this, '_toUtf8_callback'), $this->SCRIPT_ENCODING);
			if (!$this->_toUtfFailed) {
				// If some key contains non-ASCII character, convert everything manually.
				$text = json_encode($resultUtf8);
				$encoding = "UTF-8";
			}
		}

		// On failure, use manual encoding.
		if ($text === null) {
			$text = $this->php2js($result);
		}

		if ($this->LOADER != "xml") {
			// In non-XML mode we cannot use plain JSON. So - wrap with JS function call.
			// If top.JsHttpRequestGlobal is not defined, loading is aborted and
			// iframe is removed, so - do not call dataReady().
			$text = ""
				. ($this->LOADER == "form"? 'top && top.JsHttpRequestGlobal && top.JsHttpRequestGlobal' : 'JsHttpRequest')
				. ".dataReady(" . $text . ")\n"
				. "";
			if ($this->LOADER == "form") {
				$text = '<script type="text/javascript" language="JavaScript"><!--' . "\n$text" . '//--></script>';
			}

			// Always return 200 code in non-XML mode (else SCRIPT does not work in FF).
			// For XML mode, 500 code is okay.
			$status = 200;
		}

		// Status header. To be safe, display it only in error mode. In case of success
		// termination, do not modify the status (""HTTP/1.1 ..." header seems to be not
		// too cross-platform).
		if ($this->RESULT === null) {
			//начиная с php 5.3 только cgi-fcgi, nginx запрашивает HTTP/1.0 у апача
			if (substr(PHP_SAPI, 0, 3) == 'cgi')
				header ('Status: '.$status);
			else
				header($_SERVER['SERVER_PROTOCOL'].' '.$status);
		}

		// In XMLHttpRequest mode we must return text/plain - damned stupid Opera 8.0. :(
		$ctype = !empty($this->_contentTypes[$this->LOADER])? $this->_contentTypes[$this->LOADER] : $this->_contentTypes[''];
		header("Content-type: $ctype; charset=$encoding");

		return $text;
	}


	/**
	 * Internal function, used in array_walk_recursive() before json_encode() call.
	 * If a key contains non-ASCII characters, this function sets $this->_toUtfFailed = true,
	 * becaues array_walk_recursive() cannot modify array keys.
	 */
	function _toUtf8_callback(&$v, $k, $fromEnc)
	{
		if ($v === null || is_bool($v)) return;
		if ($this->_toUtfFailed || !is_scalar($v) || strpbrk($k, $this->_nonAsciiChars) !== false) {
			$this->_toUtfFailed = true;
		} else {
			$v = $this->_unicodeConv($fromEnc, 'UTF-8', $v);
		}
	}


	/**
	 * Decode all %uXXXX entities in string or array (recurrent).
	 * String must not contain %XX entities - they are ignored!
	 */
	private function _ucs2EntitiesDecode($data)
	{
		if (is_array($data)) {
			$d = array();
			foreach ($data as $k=>$v) {
				$d[$this->_ucs2EntitiesDecode($k)] = $this->_ucs2EntitiesDecode($v);
			}
			return $d;
		} else {
			if (strpos($data, '%u') !== false) { // improve speed
				$data = json_decode(str_replace('%', '\\', sprintf('"%s"',addslashes($data))));
				$data = $this->_unicodeConv('UTF-8', $this->SCRIPT_ENCODING, $data);
			}
			return $data;
		}
	}


	/**
	 * Wrapper for iconv() or mb_convert_encoding() functions.
	 * This function will generate fatal error if none of these functons available!
	 *
	 * @see iconv()
	 */
	private function _unicodeConv($fromEnc, $toEnc, $v)
	{
		if ($this->_unicodeConvMethod == 'iconv')
			return iconv($fromEnc, $toEnc, $v);
		return mb_convert_encoding($v, $toEnc, $fromEnc);
	}

}

?>
