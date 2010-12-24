<?php

require_once LIBPATH.'/utils.php';
require_once dirname(__FILE__).'/Display.php';

/**
 * Класс, на основе которого создаются остальные<br>
 * Содержит набор полей для заполнения пользователем
 *
 */
class Scaffold_Field_Info
{
	/** @var boolean скрытое поле */
	public $hide = null;
	/** @var Scaffold_Display условие показа */
	public $disp = false;
	/** @var string класс поля */
	public $type = false;
	/** @var string параметры класса */
	public $typeParams = false;
	/** @var string фильтр */
	public $filter = false;
	/** @var array настройки зависимостей */
	public $foreign = false;

	/** @var array Данные из базы */
	public $fiendInfo;

	/** @var string Имя таблицы */
	public $table;
	/** @var string Имя первичного ключа */
	public $primaryKey;
	/** @var ScaffoldController Объект таблицы */
	public $tableObject;

	/** @var string Имя поля */
	public $name = '';
	/** @var string Дефолтовое значение */
	public $default = '';
	/** @var string Заголовок колонки */
	public $title = '';
	/** @var string Описание колонки */
	public $desc = '';
	/** @var bool|string Добавить класс колонке */
	public $class = false;
	/** @var bool Обязательная колонка */
	public $required = false;
	/** @var bool использовать label */
	public $label = true;

}

/**
 * Базовый класс для всех типов полей
 * Не содержит никаких проверок, оформлений, преобразований
 *
 */
class Scaffold_Field extends Scaffold_Field_Info
{

	/**
	 * Создает полноценное поле из данных о пользователе
	 *
	 * @param Scaffold_Field_Info $info Информация о поле
	 */
	public function __construct($info)
	{
		$vars = get_class_vars('Scaffold_Field_Info');
		foreach ($vars as $k=>$v)
			$this->$k = $info->$k;
		$this->name = $info->fiendInfo['Field'];
		$this->default = $info->fiendInfo['Default'];
		if (!$this->title)
			$this->title = $this->name;
		//для совместимости
		if ($this->disp === false)
		{
			$this->disp = new Scaffold_Display();
			$this->disp->list =
			$this->disp->edit =
			$this->disp->new =
			$this->disp->multiedit =
			$this->disp->multidel = !$this->hide;
		}
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
		return $value===null ? ( $this->fiendInfo['Null'] == 'YES' ? 'NULL' : '-')
			: QFW::$view->esc($value);
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
		return '<input type="text" name="'.$this->editName($id).'"
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
		if ($this->required && empty($value))
			return false;
		return true;
	}

	/**
	 * Часть условия WHERE для данного фильтра
	 *
	 * @param mixed $session сохраненное в сессии значение
	 * @return DbSimple_SubQuery часть запроса к базе данных
	 */
	public function filterWhere($session)
	{
		return QFW::$db->subquery('?# LIKE ?', 
			array($this->table=>$this->name), $session.'%');
	}

	/**
	 * Формирует поле ввода для фильтра
	 *
	 * @param mixed $session сохраненные в сесии данные
	 * @return string часть формы для фильтра
	 */
	public function filterForm($session)
	{
		return '<input type="text" name="filter['.$this->name.']" '.
			'default="'.$session.'" label="'.$this->title.': ^" />';
	}

	/**
	 * Обработка значения перед изменением в таблице
	 *
	 * @param string $id первичный ключ или -1 для новой записи
	 * @param string|false $value исходное значение или false при удалении
	 * @param string $old Старое значение
	 * @return string обработанное значение
	 */
	public function proccess($id, $value, $old)
	{
		if ($this->fiendInfo['Null'] == 'YES' && $value=='')
			return null;
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

	/**
	 * Строит стандартный селект
	 *
	 * @param string $id первичный ключ
	 * @param array $data массив ключ=>значение
	 * @param scalar $cur текущий элемент
	 * @return string блок селекта
	 */
	protected function selectBuild($id, $data, $cur, $null=true)
	{
		$text = '<select name="'.$this->editName($id).'">';
		if ($null)
			$text.= '<option value="0"'.
				(!isset($data[$cur]) ? ' selected="selected"' : '').
					'> -- Не указано -- </option>';
		foreach ($data as $i=>$v)
			$text.= '<option value="'.$i.'"'.
				($i == $cur ? ' selected="selected"' : '').
				'>'.QFW::$view->esc($v).'</option>';
		$text.= '</select>';
		return $text;
	}

	/**
	 * Имя поля для формы
	 *
	 * @param string $id первичный ключ
	 * @return string Имя поля в name
	 */
	protected function editName($id)
	{
		return 'data['.$id.']['.$this->name.']';
	}

}

//Сервисные классы

/**
 * Класс для главного поля
 */
class Scaffold_Parent extends Scaffold_Field
{
	public function proccess($id, $value, $old)
	{
		return $_SESSION['scaffold'][$this->table]['parent'];
	}

	public function validator($id, $value)
	{
		$res = parent::validator($id, $value);
		if ($res !== true)
			return $res;
		//если у нас будет потеря сессии, то случится фигня
		return isset($_SESSION['scaffold'][$this->table]['parent']);
	}
	
	public function filterForm($session)
	{
		return '';
	}
}

/**
 * Класс для зависимых полей
 * формирует select со значениями из другой таблицы
 */
class Scaffold_Foreign extends Scaffold_Field
{
	/** @var array Зависимые поля */
	protected $lookup;

	/** @var bool Может ли быть нулевое значение */
	protected $isnull;

	public function __construct($info, $where = DBSIMPLE_SKIP)
	{
		if (!empty($info->typeParams))
			$where = $info->typeParams;
		$this->isnull = $info->foreign['null'];
		parent::__construct($info);
		$this->lookup = QFW::$db->selectCol('SELECT ?# AS ARRAY_KEY_1, ?# FROM ?# {WHERE ?s}',
			$info->foreign['key'], $info->foreign['field'], $info->foreign['table'], $where);
	}

	public function editor($id, $value)
	{
		if (isset($_POST['data'][$id][$this->name]))
			$value = $_POST['data'][$id][$this->name];
		return $this->selectBuild($id, $this->lookup, $value, $this->isnull);
	}

	public function validator($id, $value)
	{
		$res = parent::validator($id, $value);
		if ($res !== true)
			return $res;
		return $this->isnull || !empty($value);
	}

}

/**
 * Сервисный классс для полей, вводимых пользователем
 * <br>пока только обрезка при выводе, так как обязательно нагадят :)
 */
abstract class Scaffold_UserInput extends Scaffold_Field
{
	/** @var integer До скольки обрезать */
	private $trim;
	
	public function __construct($info)
	{
		parent::__construct($info);
		$this->trim = isset($info->typeParams['trim']) ? $info->typeParams['trim'] : 80;
	}

	public function display($id, $value)
	{
		return QFW::$view->esc(my_trim($value, $this->trim));
	}
}

//Классы для различных типов полей из базы данных
//Соответствие в функции ScaffoldController::getFieldClass


/**
 * Пока тестовый класс для типа TEXT
 */
class Scaffold_Text extends Scaffold_UserInput
{
	/** @var integer Сколько строк */
	private $rows;
	/** @var integer Сколько колонок */
	private $cols;

	public function __construct($info)
	{
		parent::__construct($info);
		$this->rows = isset($info->typeParams['rows']) ? $info->typeParams['rows'] : 10;
		$this->cols = isset($info->typeParams['cols']) ? $info->typeParams['cols'] : 80;
	}

	public function editor($id, $value)
	{
		return '<textarea name="'.$this->editName($id).'" '.
			'rows="'.$this->rows.'" cols="'.$this->cols.'">'.
			QFW::$view->esc($value).'</textarea>';
	}
}

/**
 * Класс для типа Int
 */
class Scaffold_Int extends Scaffold_Field
{
	public function validator($id, $value)
	{
		$res = parent::validator($id, $value);
		if ($res !== true)
			return $res;
		if (!$this->required && empty($value))
			return true;
		return is_numeric($value);
	}
}

/**
 * Класс для типа Varchar
 */
class Scaffold_Varchar extends Scaffold_Field
{
	/** @var integer размер поля в базе */
	private $size;

	public function __construct($info, $size = 100)
	{
		if (!empty($info->typeParams) && is_numeric($info->typeParams))
			$size = $info->typeParams;
		parent::__construct($info);
		$this->size = $size;
	}

	public function validator($id, $value)
	{
		$res = parent::validator($id, $value);
		if ($res !== true)
			return $res;
		return mb_strlen($value) <= $this->size;
	}
}

/**
 * Класс для типа Char - полностью аналогичен Varchar
 */
class Scaffold_Char extends Scaffold_Varchar {}

/**
 * Класс для типа ENUM
 */
class Scaffold_Enum extends Scaffold_Field
{
	/** @var array что в перечислении */
	private $items;

	public function __construct($info, $items)
	{
		parent::__construct($info);
		$items = str_getcsv($items, ',', "'");
		$this->items = array_combine($items, $items);
	}

	public function editor($id, $value)
	{
		if (isset($_POST['data'][$id][$this->name]))
			$value = $_POST['data'][$id][$this->name];
		return $this->selectBuild($id, $this->items, $value, false);
	}

}

//Классы для других типов полей, указываемых пользователем

class Scaffold_Checkbox extends Scaffold_Field
{

	public function display($id, $value)
	{
		return $value ? 'Да' : 'Нет';
	}

	public function editor($id, $value)
	{
		return '<input type="hidden" name="'.$this->editName($id).'" value="0" />
			<input type="checkbox" name="'.$this->editName($id).'" value="1" label="'.$this->title.'"
				default="'.($value?'checked':'').'" />';
	}

	/**
	 * Часть условия WHERE для данного фильтра
	 *
	 * @param mixed $session сохраненное в сессии значение
	 * @return DbSimple_SubQuery часть запроса к базе данных
	 */
	public function filterWhere($session)
	{
		if ($session == 0)
			return QFW::$db->subquery('');
		return QFW::$db->subquery('?# LIKE ?',
			array($this->table=>$this->name), $session==1 ? '1' : '0');
	}

	/**
	 * Формирует поле ввода для фильтра
	 *
	 * @param mixed $session сохраненные в сесии данные
	 * @return string часть формы для фильтра
	 */
	public function filterForm($session)
	{
		return '<fieldset>'.$this->title.':
			<label><input type="radio" name="filter['.$this->name.']" value=""'.($session==0 ? ' checked': '').'> любой</label>
			<label><input type="radio" name="filter['.$this->name.']" value="1"'.($session==1 ? ' checked': '').'> установлен</label>
			<label><input type="radio" name="filter['.$this->name.']" value="-1"'.($session==-1 ? ' checked': '').'> сброшен</label>
		</fieldset>';
	}

}

/**
 * Дата и время
 */
class Scaffold_Datetime extends Scaffold_Field
{

	public function __construct($info)
	{
		parent::__construct($info);
		if ($this->default == 'CURRENT_TIMESTAMP')
			$this->default = date('Y-m-d H:i:s');
	}

	public function editor($id, $value)
	{
		return QFW::$view->
			assign('id', $id)->
			assign('name', $this->editName($id))->
			assign('value', $value)->
			fetch('scaffold/fields/dateedit.php');
	}

}

class Scaffold_Timestamp extends Scaffold_Datetime {}


/**
 * Класс для поля, в котором хранится имя файла,
 * загружаемого на сервер
 */
class Scaffold_File extends Scaffold_Field
{
	/** @var string Путь к директории, где хранятся файлы */
	protected $path;
	/** @var string Первичный ключ таблицы */
	protected $prim;
	/** @var bool Скачиваемый (доступен извне) */
	protected $download;
	/** @var function Генерит имя файла */
	protected $genFunc;
	/** @var string параметр accept */
	protected $accept;

	/**
	 * Проверяет параметры для файлового поля
	 *
	 * @param Scaffold_Field_Info $info Информация о поле
	 */
	public function __construct($info)
	{
		parent::__construct($info);
		$this->label = false;
		if (empty ($info->typeParams['path']))
			throw new Exception('Не указана директория для фалов', 1);
		if (!is_dir($info->typeParams['path']))
			throw new Exception('Неверная директория для файлов '.$info->typeParams['path'], 1);
		if (!is_writable($info->typeParams['path']))
			throw new Exception('Нельзя писать в директорию файлов '.$info->typeParams['path'], 1);
		$this->accept = !empty($info->typeParams['accept']) ? 'accept="'.$info->typeParams['accept'].'"' : '';
		$this->path = $info->typeParams['path'];
		$this->genFunc = !empty($info->typeParams['genFunc']) ? $info->typeParams['genFunc'] : false;
		$this->prim = $info->primaryKey;
		if (strpos($info->typeParams['path'], DOC_ROOT) === 0)
			$this->download = substr($info->typeParams['path'], strlen(DOC_ROOT) );
		else
			$this->download = false;
	}

	public function editor($id, $value)
	{
		return '<input type="file" name="f'.$this->editName($id).'" '.$this->accept.' />
				<input type="hidden" name="'.$this->editName($id).'" value="0" />
				<input type="checkbox" name="'.$this->editName($id).'" value="1" label="Удалить" />'.
				'<div>'.$this->display($id, $value).'</div>';
	}

	public function validator($id, $value)
	{
		$res = parent::validator($id, $value);
		if ($res !== true)
			return $res;
		//оставляем старый файл
		if ($this->postField($id, 'error') == 4)
			return true;
		if ($this->postField($id, 'error') != 0)
			return 'Ошибка при загрузке файла '.$this->title;
		return is_uploaded_file($this->postField($id, 'tmp_name'));
	}

	public function proccess($id, $value, $old)
	{
		//если запись удалили
		if ($value === false && is_file($this->path.'/'.$old))
			unlink($this->path.'/'.$old);
		//оставляем старое значение
		if ($this->postField($id, 'error') == 4 && !$value)
			return $old ? $old : '';
		//удяляем старый
		if (is_file($this->path.'/'.$old))
			unlink($this->path.'/'.$old);
		//флаг что удалили
		if ($value)
			return '';
		//генерим новое имя
		$info = pathinfo($this->postField($id, 'name'));
		if (empty($info['extension']))
			return '';
		$p = $id == -1 ? time() : $id;
		$new_name = $this->genFunc ? call_user_func($this->genFunc, $this->name, $id, '.'.$info['extension']) : $this->name.'_'.$p.'.'.$info['extension'];
		move_uploaded_file($this->postField($id, 'tmp_name'), $this->path.'/'.$new_name);
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

	/**
	 * Имя поля для формы
	 *
	 * @param string $id первичный ключ
	 * @param string $field имя поля в массиве files
	 * @return mixed значение
	 */
	protected function postField($id, $field)
	{
		return isset($_FILES['fdata'][$field][$id][$this->name]) ?
			$_FILES['fdata'][$field][$id][$this->name] : '';
	}

}

class Scaffold_Image extends Scaffold_File
{
	public function __construct($info)
	{
		if (empty($info->typeParams['accept']))
			$info->typeParams['accept'] = 'image/*';
		parent::__construct($info);
	}

	public function display($id, $value)
	{
		if (!$value || !is_file($this->path.'/'.$value))
			return '-нет-';
		if ($this->download)
			return '<img src="'.$this->download.'/'.$value.'" />';
		return $value;
	}

	public function validator($id, $value)
	{
		$res = parent::validator($id, $value);
		if ($res !== true)
			return $res;
		//флаг что удалили
		if ($value)
			return true;
		if ($this->postField($id, 'error') == 4)
			return true;
		$ext = $this->getImgType($this->postField($id, 'tmp_name'));
		if (!$ext)
			return 'Картинка должна быть';
		return true;
	}

	public function proccess($id, $value, $old)
	{
		//если запись удалили
		if ($value === false && is_file($this->path.'/'.$old))
			unlink($this->path.'/'.$old);
		//оставляем старое значение
		if ($this->postField($id, 'error') == 4 && !$value)
			return $old ? $old : '';
		//удяляем старый
		if (is_file($this->path.'/'.$old))
			unlink($this->path.'/'.$old);
		//флаг что удалили
		if ($value)
			return '';
		//генерим новое имя
		$ext = $this->getImgType($this->postField($id, 'tmp_name'));
		$p = $id == -1 ? time() : $id;
		$new_name = $this->genFunc ? call_user_func($this->genFunc, $this->name, $id, $ext) : $this->name.'_'.$p.$ext;
		move_uploaded_file($this->postField($id, 'tmp_name'), $this->path.'/'.$new_name);
		return $new_name;
	}

	private function getImgType($name)
	{
		$info = getimagesize($name);
		if (!$info)
			return false;
		$ext = image_type_to_extension($info[2]);
		return $ext;
	}

}

?>
