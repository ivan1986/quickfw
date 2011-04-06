<?php

/**
 * Класс для работы с конфигом
 */
class QuickFW_Config implements ArrayAccess
{
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
		{
			//пробуем загрузить файл
			$file = $this->dir.'/'.$offset.'.php';
			$data = array();
			if (is_file($file))
				$data = include($file);
			if ($data == 1 && isset($config))
				$data = $config;
			$hf = $this->dir.'/'.$offset.'.'.$_SERVER['HTTP_HOST'].'.php';
			if (is_file($hf))
			{
				$data = include($hf);
				if ($data == 1 && isset($config))
					$data = $config;
			}
			$this->data[$offset] = is_object($data) ? $data : (
				is_array($data) ? new self($data, $this->dir.'/'.$offset) :
				$data);
		}
		return $this->data[$offset];
	}
	public function offsetExists($offset) { return isset($this->data[$offset]); }
	public function offsetUnset($offset) { unset($this->data); }

	//as prop
	public function __get($offset) { return $this->offsetGet($offset); }
	public function __set($offset, $data) { return $this->offsetSet($offset, $data); }
	public function __isset($offset) { return $this->offsetExists($offset); }
	public function __unset($offset) { return $this->offsetUnset($offset); }

}
