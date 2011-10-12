<?php
/**
 * Группа значений, от которых зависит показ поля
 *
 * @author ivan
 */
class Scaffold_Display
{
	public $list;
	public $edit;
	public $new;
	public $multiedit;
	public $multidel;
	/** @var bool текущее состояние */
	private static $cur;

	/**
	 * Фабрика
	 *
	 * @return Scaffold_Display
	 */
	public static function get() { return new self; }

	public function __construct()
	{
		self::$cur = true;
		$this->edit = $this->list = $this->multiedit = $this->multidel = $this->new = true;
	}

	public function tedit($v=null) { $this->edit = $v===null ? self::$cur : (bool)$v; return $this; }
	public function tlist($v=null) { $this->list = $v===null ? self::$cur : (bool)$v; return $this; }
	public function tnew($v=null) { $this->new = $v===null ? self::$cur : (bool)$v; return $this; }
	public function tmultiedit($v=null) { $this->multiedit = $v===null ? self::$cur : (bool)$v; return $this; }
	public function tmultidel($v=null) { $this->multidel = $v===null ? self::$cur : (bool)$v; return $this; }

	/**
	 * Показывать следующие поля
	 *
	 * @param $v bool показывать
	 * @return Scaffold_Display this
	 */
	public function show($v=true)
	{
		self::$cur = $v;
		return $this;
	}

	/**
	 * Скрывать следующие поля
	 *
	 * @param $v bool скрывать
	 * @return Scaffold_Display this
	 */
	public function hide($v=true)
	{
		self::$cur = !$v;
		return $this;
	}

}

