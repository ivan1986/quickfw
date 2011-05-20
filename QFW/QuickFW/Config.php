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
			$new = array();
			if (is_file($file))
				$new = include($file);
			if ($new === 1 && isset($config))
				$new = $config;
			if (!empty($new))
				$data = (is_array($data) && is_array($new)) ?
					array_merge_replace_recursive($data, $new) : $new;
		}
		return $data;
	}

	//as prop
	public function __get($offset) { return $this->offsetGet($offset); }
	public function __set($offset, $data) { return $this->offsetSet($offset, $data); }
	public function __isset($offset) { return $this->offsetExists($offset); }
	public function __unset($offset) { return $this->offsetUnset($offset); }

}

/**
 * Merges any number of arrays of any dimensions, the later overwriting
 * previous keys, unless the key is numeric, in whitch case, duplicated
 * values will not be added.
 *
 * The arrays to be merged are passed as arguments to the function.
 *
 * @access public
 * @return array Resulting array, once all have been merged
 */
function array_merge_replace_recursive()
{
	// Holds all the arrays passed
	$params = func_get_args();
	// First array is used as the base, everything else overwrites on it
	$return = array_shift($params);
	// Merge all arrays on the first array
	foreach ($params as $array)
		foreach ($array as $key => $value)
			// Numeric keyed values are added (unless already there)
			if (is_numeric($key) && (!in_array($value, $return)))
				if (is_array($value))
					$return[] = array_merge_replace_recursive($return[$key], $value);
				else
					$return[] = $value;
			else // String keyed values are replaced
				if (isset($return[$key]) && is_array($value) && is_array($return[$key]))
					$return[$key] = array_merge_replace_recursive($return[$key], $value);
				else
					$return[$key] = $value;
	return $return;
}
