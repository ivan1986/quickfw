<?php

/**
 * Используйте константу DBSIMPLE_SKIP в качестве подстановочного значения чтобы пропустить опцональный SQL блок.
 */
define('DBSIMPLE_SKIP', log(0));

class QuickFW_AutoDbSimple
{
	protected $DbSimple, $DSN;

	public function __construct($dsn)
	{
		$this->DbSimple  = null;
		$this->DSN       = $dsn;
	}

	public function __call($method, $params)
	{
		if ($this->DbSimple === null)
			$this->connect($this->DSN);
		$result = call_user_func_array(array(&$this->DbSimple, $method), $params);
		return $result;
	}

	protected function connect($dsn)
	{
		$parsed = $this->parseDSN($dsn);
		if (!$parsed)
			$this->errorHandler('Ошибка разбора строки DSN',$dsn);
		if (!isset($parsed['scheme']) || !is_file(QFWPATH.'/DbSimple/'.ucfirst($parsed['scheme']).'.php'))
			$this->errorHandler('Невозможно загрузить драйвер базы данных',$parsed);
		require_once QFWPATH.'/DbSimple/'.ucfirst($parsed['scheme']).'.php';
		$class = 'DbSimple_'.ucfirst($parsed['scheme']);
		$this->DbSimple = new $class($parsed);
		if (isset($parsed['prefix']))
			$this->DbSimple->setIdentPrefix($parsed['prefix']);
		$this->DbSimple->setCachePrefix('db_'.crc32($parsed['dsn']).'_');
		$this->DbSimple->setErrorHandler(array(&$this, 'errorHandler'));
		$this->DbSimple->query('SET NAMES '.isset($parsed['enc'])?$parsed['enc']:'UTF8');
	}

	/**
	 * Функция обработки ошибок - выводит сообщение об ошибке на тестовом
	 * на продакшене показывает 404 и пишит в sql.log
	 * Все вызовы без @ прекращают выполнение скрипта
	 */
	protected function errorHandler($msg, $info)
	{
		// Если использовалась @, ничего не делать.
		if (!error_reporting()) return;
		if (QFW::$config['release'])
		{
			require_once LIBPATH.'/Log.php';
			Log::log('SQL Error - '.$msg,'sql');
			QFW::$router->show404();
		}
		// Выводим подробную информацию об ошибке.
		echo "SQL Error: $msg<br><pre>";
		print_r($info);
		echo "</pre>";
		exit();
	}

	/**
	 * array parseDSN(string $dsn)
	 * Разбирает строку DSN в массив параметров подключения к базе
	 */
	protected function parseDSN($dsn)
	{
		$parsed = parse_url($dsn);
		if (!$parsed)
			return null;
		$params = null;
		if (!empty($parsed['query']))
		{
			parse_str($parsed['query'], $params);
			$parsed += $params;
		}
		$parsed['dsn'] = $dsn;
		return $parsed;
	}
}

?>