<?php

/**
 * Use this constant as placeholder value to skip optional SQL block [...].
 */
define('DBSIMPLE_SKIP', log(0));

class QuickFW_AutoDbSimple
{
	protected $DbSimple, $DSN;

	function __construct($dsn)
	{
		$this->DbSimple  = null;
		$this->DSN       = $dsn;
	}

	function __call($method, $params)
	{
		if ($this->DbSimple === null)
			$this->connect($this->DSN);
		$result = call_user_func_array(array(&$this->DbSimple, $method), $params);
		return $result;
	}

	protected function connect($dsn)
	{
		$parsed = $this->parseDSN($dsn);
		if (!$parsed) {
			$dummy = null;
			return $dummy;
		}
		require_once QFWPATH.'/DbSimple/'.ucfirst($parsed['scheme']).'.php';
		$class = 'DbSimple_'.ucfirst($parsed['scheme']);
		$this->DbSimple = new $class($parsed);
		if (isset($parsed['ident_prefix'])) {
			$this->DbSimple->setIdentPrefix($parsed['ident_prefix']);
		}
		$this->DbSimple->setCachePrefix(md5(serialize($parsed['dsn'])));
		$this->DbSimple->setErrorHandler(array(&$this, 'errorHandler'));
		$this->DbSimple->query("SET NAMES ".$parsed['enc']);
	}

	public function errorHandler($msg, $info)
	{
		// Если использовалась @, ничего не делать.
		if (!error_reporting()) return;
		if (QFW::$config['release'])
			QFW::$router->show404();
		// Выводим подробную информацию об ошибке.
		echo "SQL Error: $msg<br><pre>";
		print_r($info);
		echo "</pre>";
		exit();
	}

	/**
	 * array parseDSN(mixed $dsn)
	 * Parse a data source name.
	 * See parse_url() for details.
	 */
	function parseDSN($dsn)
	{
		if (is_array($dsn)) return $dsn;
		$parsed = parse_url($dsn);
		if (!$parsed) return null;
		$params = null;
		if (!empty($parsed['query'])) {
			parse_str($parsed['query'], $params);
			$parsed += $params;
		}
		$parsed['dsn'] = $dsn;
		return $parsed;
	}
}

?>