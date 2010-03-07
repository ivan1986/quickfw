<?php

require_once 'Templater.php';

/**
 * Делегат отложенного вызова функции или метода
 * с дополнительным первым параметром
 */
class PlainView_Delegate
{
	/** @var object|string|false объект, чей метод вызываем */
	private $obj;
	/** @var string имя метода */
	private $m;
	/** @var mixed[] параметры */
	private $args;

	public function  __construct($obj = false)
	{
		$this->obj = $obj;
		$this->m = false;
		$this->args = array();
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
		array_unshift($this->args, $data);
		return call_user_func_array($m, $this->args);
	}

}

class Templater_PlainView extends Templater
{

	public function fetch($tmpl)
	{
		extract($this->_vars, EXTR_OVERWRITE);
		$P=&$this->P;
		ob_start();
		include($this->_tmplPath . '/' . $tmpl);
		return ob_get_clean();
	}

	/** @var PlainView_Delegate[] */
	private $callers = array();

	private function begin($obj = false)
	{
		$caller = new PlainView_Delegate($obj);
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

}

?>
