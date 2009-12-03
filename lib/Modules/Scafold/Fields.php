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

	/**
	 * Получает массив данных о поле
	 *
	 * @param array $info array(<br>
	 * 'table' => имя таблицы,<br>
	 * 'base' => результат SHOW FIELDS IN table (для этого поля),<br>
	 * 'field' => подмасиив fields (для этого поля),<br>
	 * )
	 */
	public function __construct($info)
	{
		$this->name = $info['base']['Field'];
		$this->default = $info['base']['Default'];
	}

	/**
	 * Выводит значение поля
	 *
	 * @param string $id первичный ключ
	 * @param string $value значение поля
	 * @return string То, что будет показано
	 */
	public function display($id, $value)
	{
		return $value===null ? '-' : QFW::$view->esc($value);
	}

	/**
	 * Выводит редактор для поля
	 *
	 * @param string $id первичный ключ или -1 для новой записи
	 * @param string $value значение поля
	 * @return string html код редактора
	 */
	public function editor($id, $value)
	{
		return '<input type="text" name="data['.$this->name.']"
			   default="'.QFW::$view->esc($value).'" />';
	}

	/**
	 * Проверяет корректность ввода поля
	 *
	 * @param string $id первичный ключ или -1 для новой записи
	 * @param string $value проверяемое значение
	 * @return bool|string true, если правильно<br>
	 * false, для стандартного сообщения об ошибке<br>
	 * строка для сообщения об ошибке
	 */
	public function validator($id, $value)
	{
		return true;
	}

	/**
	 * Обработка значения перед изменением в таблице
	 *
	 * @param string $id первичный ключ или -1 для новой записи
	 * @param string|false $value исходное значение или false при удалении
	 * @return string обработанное значение
	 */
	public function proccess($id, $value)
	{
		return $value;
	}

	public function action($id, $action='do')
	{
		die('Был вызван метод '.$action.' для поля '. $this->name);
	}

	/**
	 * Вызывается при заполнениии формы для вставки
	 *
	 * @return string дефолтовое значение
	 */
	public function def()
	{
		return $this->default;
	}

}

//Классы для различных типов полей
//Соответствие в функции ScafoldController::getFieldClass

class Scafold_Foregen extends Scafold_Field
{
	/** @var array Зависимые поля */
	protected $lookup;

	public function __construct($info)
	{
		parent::__construct($info);
		$f = $info['field']['foregen'];
		$this->lookup = QFW::$db->selectCol('SELECT ?# AS ARRAY_KEY_1, ?# FROM ?#',
			$f['key'], $f['field'], $f['table']);
	}

	public function editor($id, $value)
	{
		$text = '<select name="data['.$this->name.']">'.
				'<option value="0"'.
				(!isset($this->lookup[$value]) ? ' selected="selected"' : '').
					'> -- Не указано -- </option>';
		foreach ($this->lookup as $i=>$v)
			$text.= '<option value="'.$i.'"'.
				($i == $value ? ' selected="selected"' : '').
				'>'.QFW::$view->esc($v).'</option>';
		$text.= '</select>';
		return $text;
	}

}

class Scafold_Enum extends Scafold_Field
{
	
	public function display($id, $value)
	{
		return '111111';
	}

}

?>
