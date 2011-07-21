<?php

require_once 'Templater.php';

/**
 * Делегат отложенного вызова функции или метода
 */
class PlainView_Delegate
{
	/** @var object|string|false объект, чей метод вызываем */
	private $obj;
	/** @var string имя метода */
	private $m;
	/** @var mixed[] параметры */
	private $args;
	/** @var bool флаг замены */
	private $replase;

	/**
	 *
	 * @param object|string|false $obj объект, или имя класса
	 * @param bool $replase заменять первый параметр
	 *        или просто добавлять в начало
	 */
	public function  __construct($obj, $replase)
	{
		$this->obj = $obj;
		$this->m = false;
		$this->args = array();
		$this->replase = $replase;
	}

	public function __call($m, $args)
	{
		$this->m = $m;
		$this->args = $args;
	}

	/**
	 * Проверяет, можно ли вызвать сохраненный метод
	 *
	 * @return bool можно вызывать
	 */
	public function callable()
	{
		return $this->m !== false;
	}

	/**
	 * Вызывает сохраненную функцию
	 *
	 * @param mixed $data первый параметр
	 * @return mixed то, что вернула вызванная функция
	 */
	public function run($data)
	{
		if (!$this->m)
			return false;
		$m = $this->obj ? array($this->obj, $this->m) : $this->m;
		if ($this->replase)
			$this->args[0] = $data;
		else
			array_unshift($this->args, $data);
		return call_user_func_array($m, $this->args);
	}

}

class Templater_PlainView extends Templater
{

	public function fetch($tmpl, $vars=array())
	{
		extract(static::$global_vars, EXTR_OVERWRITE);
		extract($this->_vars, EXTR_OVERWRITE);
		extract($vars, EXTR_OVERWRITE);
		$P=&$this->P;
		ob_start();
		include($this->_tmplPath . '/' . $tmpl);
		return ob_get_clean();
	}

	/** @var PlainView_Delegate[] */
	private $callers = array();

	/**
	 *
	 * @param object|string|false $obj объект, или имя класса
	 * @param bool $replase заменять первый параметр или просто добавлять
	 * @return PlainView_Delegate
	 */
	private function begin($obj = false, $replase = false)
	{
		$caller = new PlainView_Delegate($obj, $replase);
		$this->callers[] = $caller;
		ob_start();
		return $caller;
	}

	private function end()
	{
		$data = ob_get_clean();
		$caller = array_pop($this->callers);
		$caller instanceof PlainView_Delegate;
		echo $caller->callable() ? $caller->run($data) : $data;
	}

	public function extend($tpl)
	{
		return $this->begin($this, false)->extend_call($tpl);
	}
	public function extend_call($data, $tpl)
	{
		return $this->fetch($tpl);
	}
	private $_blocks = array();
	public function bl($name)
	{
		return $this->begin($this, false)->bl_call($name);
	}
	public function bl_call($data, $name)
	{
		if (array_key_exists($name, $this->_blocks) === false)
			$this->_blocks[$name] = array();

		if (in_array($data, $this->_blocks[$name]) === false)
			array_push($this->_blocks[$name], $data);

		return $this->_blocks[$name][0];
	}

}

?>
