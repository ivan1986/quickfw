<?php

/**
 * Базовый класс для всех типов полей
 * Не содержит никаких проверок, оформлений, преобразований
 *
 */
class Scafold_Field
{
	/** @var string Имя поля */
	protected $name;
	/** @var string Дефолтовое значение */
	protected $default;

	public function __construct($info)
	{
		$this->name = $info['Field'];
		$this->default = $info['Default'];
	}

	public function display($value)
	{
		return $value===null ? '-' : QFW::$view->esc($value);
	}

	public function editor($value)
	{
		return '<input type="text" name="data['.$this->name.']"
			   default="'.QFW::$view->esc($value).'" />';
	}

	public function validator($value)
	{
		return true;
	}

	public function proccess($value)
	{
		return $value;
	}

	public function def()
	{
		return $this->default;
	}

}

//Классы для различных типов полей
//Соответствие в функции ScafoldController::getFieldClass

class Scafold_Enum extends Scafold_Field
{
	public function display($value)
	{
		return parent::display($value);
	}
	
}

?>
