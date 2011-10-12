<?php

class RecursiveArrayAccess implements ArrayAccess
{
	private $data = array();

	// necessary for deep copies
	public function __clone() {
		foreach ($this->data as $key => $value) if ($value instanceof self) $this[$key] = clone $value;
	}

	public function __construct(array $data = array()) {
		foreach ($data as $key => $value) $this[$key] = $value;
	}

	public function offsetSet($offset, $data) {
		if (is_array($data)) $data = new self($data);
		if ($offset === null) { // don't forget this!
			$this->data[] = $data;
		} else {
			$this->data[$offset] = $data;
		}
	}

	public function toArray() {
		$data = $this->data;
		foreach ($data as $key => $value) if ($value instanceof self) $data[$key] = $value->toArray();
		return $data;
	}

	// as normal
	//public function offsetGet($offset) { return isset($this->data[$offset]) ? $this->data[$offset] : null; }
	public function offsetGet($offset) { return $this->data[$offset]; }
	public function offsetExists($offset) { return isset($this->data[$offset]); }
	public function offsetUnset($offset) { unset($this->data[$offset]); }
	
	//as prop
	public function __get($offset) { return $this->offsetGet($offset); }
	public function __set($offset, $data) { return $this->offsetSet($offset, $data); }
	public function __isset($offset) { return $this->offsetExists($offset); }
	public function __unset($offset) { return $this->offsetUnset($offset); }

}
