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
	/** @var string Заголовок колонки */
	protected $title;

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
		$this->title = empty($info['field']['title']) ? $this->name : $info['field']['title'];
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
		die('Был вызван метод '.$action.' для поля '. $this->title);
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

/**
 * Класс для зависимых полей
 * формирует select со значениями из другой таблицы
 */
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

/**
 * Пока тестовый класс для enum
 */
class Scafold_Enum extends Scafold_Field
{
	
	public function display($id, $value)
	{
		return '111111';
	}

}

/**
 * Класс для поля, в котором хранится имя файла,
 * загружаемого на сервер
 */
class Scafold_File extends Scafold_Field
{
	/** @var string Путь к директории, где хранятся файлы */
	private $path;
	/** @var string Первичный ключ таблицы */
	private $prim;
	/** @var string Таблица */
	private $table;
	/** @var bool Скачиваемый (доступен извне) */
	private $download;

	public function __construct($info, $params)
	{
		parent::__construct($info);
		if (empty ($params['path']))
			throw new Exception('Не указана директория для фалов', 1);
		if (!is_dir($params['path']))
			throw new Exception('Неверная директория для файлов '.$params['path'], 1);
		if (!is_writable($params['path']))
			throw new Exception('Нельзя писать в директорию файлов '.$params['path'], 1);
		$this->path = $params['path'];
		$this->prim = $info['primaryKey'];
		$this->table = $info['table'];
		if (strpos($params['path'], DOC_ROOT) === 0)
			$this->download = substr($params['path'], strlen(DOC_ROOT) );
		else
			$this->download = false;
	}

	public function editor($id, $value)
	{
		return '<input type="file" name="file['.$this->name.']" />
				<input type="hidden" name="data['.$this->name.']" value="0" />
				<input type="checkbox" name="data['.$this->name.']" value="1" label="Удалить" />';
	}

	public function validator($id, $value)
	{
		//оставляем старый файл
		if ($_FILES['file']['error'][$this->name] == 4)
			return true;
		if ($_FILES['file']['error'][$this->name] != 0)
			return 'Ошибка при загрузке файла '.$this->title;
		return is_uploaded_file($_FILES['file']['tmp_name'][$this->name]);
	}

	public function proccess($id, $value)
	{
		$old = QFW::$db->selectCell('SELECT ?# FROM ?# WHERE ?#=?',
			$this->name, $this->table, $this->prim, $id);
		//если запись удалили
		if ($value === false && is_file($this->path.'/'.$old))
			unlink($this->path.'/'.$old);
		//оставляем старое значение
		if ($_FILES['file']['error'][$this->name] == 4 && !$value)
			return $old;
		//удяляем старый
		if (is_file($this->path.'/'.$old))
			unlink($this->path.'/'.$old);
		//флаг что удалили
		if ($value)
			return '';
		//генерим новое имя
		$info = pathinfo($_FILES['file']['name'][$this->name]);
		$new_name = $this->name.'_'.$id.'.'.$info['extension'];
		move_uploaded_file($_FILES['file']['tmp_name'][$this->name], $this->path.'/'.$new_name);
		return $new_name;
	}

	public function display($id, $value)
	{
		if (!$value || !is_file($this->path.'/'.$value))
			return '-нет-';
		if ($this->download)
			return '<a href="'.$this->download.'/'.$value.'">'.$value.'</a>';
		return $value;
	}

}

?>
