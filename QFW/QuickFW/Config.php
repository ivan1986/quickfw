<?php

/**
 * Класс для работы с конфигом
 */
class QuickFW_Config implements ArrayAccess
{
	/**
	 * Генерирует список файлов конфигураций
	 *
	 * @param string|false $prefix префикс
	 * @return array массив файлов
	 */
	static private function files($prefix)
	{
		if ($prefix === false)
		{
			$files = array();
			$files[] = QFWPATH.'/config.php';
			$files[] = APPPATH.'/default.php';
			if (isset($_SERVER['SERVER_NAME']))
				$files[] = APPPATH.'/serv.'.$_SERVER['SERVER_NAME'].'.php';
			if (isset($_SERVER['HTTP_HOST']))
				$files[] = APPPATH.'/host.'.$_SERVER['HTTP_HOST'].'.php';
			return $files;
		}
		$files = array();
		$files[] = $prefix.'.php';
		if (isset($_SERVER['SERVER_NAME']))
			$files[] = $prefix.'.serv.'.$_SERVER['SERVER_NAME'].'.php';
		if (isset($_SERVER['HTTP_HOST']))
			$files[] = $prefix.'.host.'.$_SERVER['HTTP_HOST'].'.php';
		return $files;
	}

	/**
	 * Генерация основного конфига
	 *
	 * @return array|mixed|QuickFW_Config вложенный массив
	 */
	static public function main()
	{
		return new self(self::loadFromFiles(self::files(false)), APPPATH.'/config');
	}

	private $data = array();
	/**
	 * @var string Директория конфигурации
	 */
	private $dir = '';

	// necessary for deep copies
	public function __clone() {
		foreach ($this->data as $key => $value)
			if ($value instanceof self)
				$this[$key] = clone $value;
	}

	public function __construct(array $data = array(), $dir='') {
		foreach ($data as $key => $value) $this[$key] = $value;
		$this->dir = $dir;
	}

	public function offsetSet($offset, $data) {
		if (is_array($data)) $data = new self($data, true);
		if ($offset === null) { // don't forget this!
			$this->data[] = $data;
		} else {
			$this->data[$offset] = $data;
		}
	}

	public function toArray() {
		$data = $this->data;
		foreach ($data as $key => $value)
			if ($value instanceof self)
				$data[$key] = $value->toArray();
		return $data;
	}

	public function __toString() {
		return var_export($this->data, true);
	}

	public function offsetGet($offset) {
		if ($this->dir && !isset($this->data[$offset]))
			$this->data[$offset] = $this->load($offset);
		return $this->data[$offset];
	}
	public function offsetExists($offset) { return isset($this->data[$offset]); }
	public function offsetUnset($offset) { unset($this->data); }

	/**
	 * Пробуем загрузить файл из текущей директории
	 *
	 * @param string $name имя файла
	 * @return array|mixed|QuickFW_Config вложенный массив
	 */
	private function load($name)
	{
		$data = self::loadFromFiles(self::files($this->dir.'/'.$name));
		return is_array($data) ? new self($data, $this->dir.'/'.$name) : $data;
	}

	/**
	 * Пробуем загрузить файл из текущей директории
	 *
	 * @param array $files файлы
	 * @return array|mixed|QuickFW_Config вложенный массив
	 */
	static private function loadFromFiles($files)
	{
		$data = array();
		foreach($files as $file)
		{
			if (is_file($file))
				$new = include($file);
			if ($new == 1 && isset($config))
				$new = $config;
			$data = array_merge_recursive($data, $new);
		}
		return $data;
	}

	//as prop
	public function __get($offset) { return $this->offsetGet($offset); }
	public function __set($offset, $data) { return $this->offsetSet($offset, $data); }
	public function __isset($offset) { return $this->offsetExists($offset); }
	public function __unset($offset) { return $this->offsetUnset($offset); }

}
