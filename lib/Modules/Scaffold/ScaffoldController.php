<?php

require_once LIBPATH.'/Modules/Scaffold/Fields.php';

/**
 * Класс для быстрого создания CRUD интерфейса к таблице
 * 
 * <br><br>По умолчанию наследует себя от класса Controller
 * базового класса, который есть в этом модуле
 * для того чтобы корректно подхватывать авторизацию и прочее.
 * <br><br>Класс предназначен для использования в админках
 * преимущественно для редакторивания справочников и
 * подобных им таблиц
 * <br><br>Пример подключения:
 * <br><br>require 'Controller.php';
 * <br>require LIBPATH.'/Modules/Scaffold/ScaffoldController.php';
 * <br>.....
 * <br>class TableController extends ScaffoldController
 *
 * @author Ivan1986
 */
abstract class ScaffoldController extends Controller
{
	/** @var string Имя таблицы */
	protected $table = '';
	/** @var string хвост запроса на выборку после FROM table */
	protected $where = '';
	/** @var integer Количество элементов на странице */
	protected $pageSize = 20;
	/** @var string Имя первичного ключа таблицы */
	protected $primaryKey = 'id';
	/** @var array Информация о полях */
	protected $fields = array();
	/** @var array Действия, производимые над каждой строчкой */
	protected $actions = array();
	/** @var array Эта таблица зависимая - данные о родительской */
	protected $parentData = false;
	/** @var array ссылка на сессию таблицы */
	protected $sess = array();

	//Опции
	/** @var bool|string Добавить внизу страницы (заголовок) */
	protected $addOnBottom = true;
	/** @var bool Показывать картинки при сортировке */
	protected $sortImages = false;

	/** @var array Массив методов */
	private $methods;
	/** @var boolean Флаг окончания настройки */
	private $setup = false;
	/** @var array Порядок столбцев */
	private $order = array();

	/**
	 * Получает данные о полях
	 *
	 * @return array Данные
	 */
	private function fields()
	{
		$f = array();
		$shema = QFW::$db->getShema();
		if ($shema == 'Mypdo' || $shema == 'Mysql')
		{	//Mysql
			$fields = QFW::$db->select('SHOW FIELDS IN ?#', $this->table);
			foreach($fields as $field)
			{
				$c = $this->getInfoClass($field['Field']);
				$c->primaryKey = $field['Key'] == 'PRI';
				$f[$field['Field']] = $this->getFieldClass($c, $field);
			}
		}
		else if ($shema == 'Litepdo' || $shema == 'Sqlite')
		{	//Sqlite
			$sql = QFW::$db->selectCell('SELECT sql FROM sqlite_master
				WHERE type=? AND name=?', 'table', str_replace('?_', 
					QFW::$db->setIdentPrefix(null), $this->table));
			//выделяем то что в скобках
			$sql = substr($sql, strpos($sql, '(')+1, -1);
			$fields = explode(',', $sql);
			foreach($fields as $field)
			{
				$field = explode(' ', trim($field), 2);
				$c = $this->getInfoClass($field[0]);
				$c->primaryKey = strpos($field[1], 'PRIMARY KEY') !== false;
				$info = array(
					'Field' => $field[0],
					'Type' => $field[1],
					'Null' => 'NO',
					'Default' => null,
					'Extra' => '',
				);
				$f[$field[0]] = $this->getFieldClass($c, $info);
			}
		}
		return $f;
	}

	/**
	 * Конструктор вызывать только после настройки таблицы
	 */
	public function  __construct()
	{
		QFW::$view->P->addCSS('built-in/scaffold.css');
		$this->session();
		//Создаем сессию для таблицы и ссылаемся на нее
		if (!isset($_SESSION['scaffold'][$this->table]))
			$_SESSION['scaffold'][$this->table] = array();
		$this->sess = &$_SESSION['scaffold'][$this->table];

		$this->setup = true;
		parent::__construct();
		$this->methods = array_flip(get_class_methods($this));

		//Получаем данные о полях
		$this->fields = $this->fields();
		foreach($this->fields as $k=>$field)
			if (get_class($field) == 'Scaffold_Field_Info')
				unset($this->fields[$k]);

		//порядок сортировки полей
		foreach($this->order as $k=>$v)
			if (!isset($this->fields[$v]))
				unset($this->order[$k]);

		$this->assignMainInfo();
	}

	/**
	 * Выставляет в шаблон общую инфу о таблице
	 */
	private function assignMainInfo()
	{
		//Общая информация о таблице
		QFW::$view->assign(array(
			'methods' => $this->methods,
			'class' => get_class($this),
			'primaryKey' => $this->primaryKey,
			'fields' => $this->fields,
			'actions' => $this->actions,
			'table' => str_replace('?_', '', $this->table),
			'session' => $this->sess,
			'options' => array(
				'addOnBottom' => $this->addOnBottom,
				'sortImages' => $this->sortImages,
			),
		));
  }

	/**
	 * Востанавливает данные сессии по умолчанию
	 *
	 * @param integer $page страница
	 */
	public function clearAction()
	{
		$this->sess = array();
		QFW::$router->redirect(Url::C());
	}

	/**
	 * Блок заглушка для всех необъявленных
	 *
	 * @return string пусто
	 */
	public function indexBlock()
	{
		return '';
	}

	/**
	 * Вывод страницы таблицы
	 *
	 * @param integer $page страница
	 */
	public function indexAction($page=1)
	{
		// считаем страницы с нуля и убираем отрицательные
		$page = max($page-1, 0);
		$state = new TemplaterState(QFW::$view);
		QFW::$view->setScriptPath(dirname(__FILE__));

		$parentWhere = DBSIMPLE_SKIP;
		//Устанавливаем фильтр
		if ($this->parentData)
		{
			$parent = QFW::$db->selectCol('SELECT ?# AS ARRAY_KEY, ?# FROM ?# ?s',
				$this->parentData['key'], $this->parentData['field'], 
				$this->parentData['table'], $this->parentData['other']);
			if (isset($_POST['parent']))
			{
				$this->sess['parent'] = $_POST['parent'];
				QFW::$router->redirect(Url::A());
			}
			if (empty($this->sess['parent']))
				$this->sess['parent'] = count($parent) ? key($parent) : 0;

			QFW::$view->assign('parent', QFW::$view->assign('parent', array(
				'list' => $parent,
				'current' => $this->sess['parent'],
			))->fetch('scaffold/parent.php'));
			$parentWhere = QFW::$db->subquery('AND ?#=?',
					array($this->table => $this->parentData['colum']),
					$this->sess['parent']);
		}

		$filter = $this->filterGen();
		$count = QFW::$db->selectCell('SELECT count(*) FROM ?# 
			WHERE ?s ?s '.$this->where, $this->table, $filter['where'], $parentWhere);

		$foreign = $this->getForeign();
		$data = QFW::$db->select('SELECT ?# ?s FROM ?# ?s
			WHERE ?s ?s '.$this->where.' ?s LIMIT ?d, ?d',
			array($this->table=>array_merge($this->order, array('*'))),
			$foreign['field'], $this->table, $foreign['join'],
			$filter['where'], $parentWhere,
			$this->getSort(),
			$page*$this->pageSize, $this->pageSize);

		if (count($filter['form']))
		{
			require_once LIBPATH.'/HTML/FormPersister.php';
			ob_start(array(new HTML_FormPersister(), 'process'));
			QFW::$view->assign('filter', $filter['form']);
		}
		//получаем пагинатор
		$pages = ceil($count/$this->pageSize);
		$pager=QFW::$router->blockRoute('helper.nav.pager', Url::A('$'), $pages, $page+1);

		return QFW::$view->assign(array(
			'data' => $data,
			'pager' => $pager,
		))->fetch('scaffold/index.php');
	}

	/**
	 * Отображает форму добавления
	 *
	 * @param int $id ключ записи для редактирования
	 */
	public function newBlock()
	{
		//инициализация FormPersister
		/*require_once LIBPATH.'/HTML/FormPersister.php';
		ob_start(array(new HTML_FormPersister(), 'process'));*/

		//получение дефолтовых значений для новой записи
		$data = array();
		$fields = array();
		//сортированные поля
		foreach($this->order as $f)
			$fields[] = $f;
		//остальные поля
		foreach ($this->fields as $f=>$info)
			if (!isset($fields[$f]))
				$fields[] = $f;
		//вынимаем с учетом default_*
		foreach($fields as $f)
			if (isset($this->methods['default_'.ucfirst($f)]))
				$data[$f] = call_user_func(array(get_class($this), 'default_'.ucfirst($f)));
			else
				$data[$f] = $this->fields[$f]->def();

		$state = new TemplaterState(QFW::$view);
		QFW::$view->setScriptPath(dirname(__FILE__));

		$this->assignMainInfo();
		return QFW::$view->assign(array(
			'id' => -1,
			'data' => $data,
		))->fetch('scaffold/edit.php');
	}

	/**
	 * Отображает форму редактирования или добавления
	 *
	 * @param int $id ключ записи для редактирования
	 */
	public function editAction($id=-1)
	{
		//инициализация FormPersister
		require_once LIBPATH.'/HTML/FormPersister.php';
		ob_start(array(new HTML_FormPersister(), 'process'));
		$errors = array();
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($_POST['data'][$id])>0)
		{
			//Обработка результата редактирования
			$data = $_POST['data'][$id];
			foreach ($data as $k=>$v)
			{
				if (isset($this->methods['validator_'.ucfirst($k)]))
					$res = call_user_func(array($this, 'validator_'.ucfirst($k)), $v, $id);
				else
					$res = $this->fields[$k]->validator($id, $v);
				if ($res !== true)
					$errors[$k] = $res;
				if ($res === false)
					$errors[$k] = 'Поле '.$this->fields[$k]->title.' имеет некорректное значение';
			}
			//Если ошибок нет, то записываем в базу изменения
			if (count($errors))
				QFW::$view->assign('errors', $errors);
			else
			{
				$old = $this->getOldVars($id);
				//Обработка данных после POST
				foreach ($this->fields as $k=>$class)
					if ($k == $this->primaryKey && !isset($data[$k]))
						continue; //не трогаем первичный ключ
					elseif (isset($this->methods['proccess_'.ucfirst($k)]))
						$data[$k] = call_user_func(array($this, 'proccess_'.ucfirst($k)), 
							isset($data[$k]) ? $data[$k] : $old[$k], $id, $old[$k]);
					else
						$data[$k] = $class->proccess($id,
							isset($data[$k]) ? $data[$k] : $old[$k], $old[$k]);

				if ($id == -1)
					$ins_id = QFW::$db->query('INSERT INTO ?#(?#) VALUES(?a)',
						$this->table, array_keys($data), array_values($data));
				else
					QFW::$db->query('UPDATE ?# SET ?a WHERE ?#=?',
						$this->table, $data, $this->primaryKey, $id);

				if (isset($this->methods['postEdit']))
					call_user_func(array($this, 'postEdit'), $id == -1 ? $ins_id : $id);

				//редирект назад
				if (!empty($this->sess['return']))
				{
					$url = $this->sess['return'];
					unset($this->sess['return']);
					QFW::$router->redirect($url);
				}
				else
					QFW::$router->redirect(Url::C('index'));

			}
		}
		
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'index'))
			$this->sess['return'] = $_SERVER['HTTP_REFERER'];

		$data = $this->getOldVars($id);

		$state = new TemplaterState(QFW::$view);
		QFW::$view->setScriptPath(dirname(__FILE__));

		return QFW::$view->assign(array(
			'id' => $id,
			'data' => $data,
		))->fetch('scaffold/edit.php');
	}

	/**
	 * Удаление строки
	 *
	 * <br>Если нужно обработать удаление как-то нестандартно,
	 * то функция должна быть перегружена
	 *
	 * @param string $id значение первичного ключа удаляемой строки
	 */
	public function deleteAction($id=0)
	{
		$old = $this->getOldVars($id);
		if (!$old)
			QFW::$router->redirect(Url::C('index'), true);
		foreach($this->fields as $k=>$v)
			$v->proccess($id, false, $old[$k]);
		QFW::$db->query('DELETE FROM ?# WHERE ?#=?',
			$this->table, $this->primaryKey, $id);
		QFW::$router->redirect(Url::C('index'), true);
	}

	/**
	 * Вызывает действие, привязанное к определенному полю
	 *
	 * @param string $name имя поля
	 * @param string $id значение первичного ключа
	 */
	public function fieldAction($name, $id)
	{
		$args = func_get_args();
		array_shift($args);
		if (isset($this->fields[$name]))
			call_user_func_array(array($this->fields[$name], 'action'), $args);
		QFW::$router->redirect(Url::C('index'), true);
	}

	/**
	 * обработка смены фильтра
	 */
	public function filterAction()
	{
		if (!empty($_POST['clear']))
		{
			$this->sess['filter'] = array();
			QFW::$router->redirect(Url::C('index'), true);
		}
		if (empty($_POST['filter']) || empty($_POST['apply']))
			QFW::$router->redirect(Url::C('index'), true);
		$this->sess['filter'] = $_POST['filter'];
		
		QFW::$router->redirect(Url::C('index'), true);
	}

	/**
	 * Устанавливает порядок сортировки
	 *
	 * @param string $field Имя поля
	 * @param string $dir Направление сортировки (ASC|DESC|)
	 */
	public function sortAction($field='', $dir='')
	{
		$this->setSort($field, $dir);
		QFW::$router->redirect(Url::C('index'), true);
	}

	////////////////////////////////////////////////////////////
	//Функции для упращения настройки таблицы - удобные сеттеры
	////////////////////////////////////////////////////////////

	/**
	 * Указывает порядок сортировки столбцев
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param array $fieldList массив с именами полей
	 * @return ScaffoldController
	 */
	protected function order($fieldList)
	{
		$this->endTest();
		$this->order = $fieldList;
		return $this;
	}

	/**
	 * Устанавливает таблицу как подчиненную
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|DbSimple_SubQuery $colum Колонка зависимости
	 * @param string|DbSimple_SubQuery $table Главная таблица
	 * @param string|DbSimple_SubQuery $id Ключ в главной таблице
	 * @param string|DbSimple_SubQuery $name Заголовок в главной таблице
	 * @param DbSimple_SubQuery $other Дополнительные условия
	 * @return ScaffoldController
	 */
	protected function parent($colum, $table, $id, $name, $other=DBSIMPLE_SKIP)
	{
		$this->parentData = array(
			'colum' => $colum,
			'field' => $name,
			'table' => $table,
			'key'   => $id,
			'other' => $other,
		);
		$this->getInfoClass($colum)->type = 'parent';
		$this->getInfoClass($colum)->hide = true;
		return $this;
	}

	/**
	 * Устанавливает поле как зависимое от другой таблицы
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string $colum Колонка
	 * @param string $table Связанная таблица
	 * @param string $id Ссылочный ключ
	 * @param string $name Значение связанного поля
	 * @param bool $notNull Не допускать пустого значения
	 * @return ScaffoldController
	 */
	protected function foreign($colum, $table, $id, $name, $notNull=false)
	{
		$this->endTest();
		$this->getInfoClass($colum)->foreign = array(
			'field' => $name,
			'table' => $table,
			'key'   => $id,
			'null'  => !$notNull,
		);
		return $this;
	}

	////////////////////////////////////////////////////////////
	// Сеттеры
	////////////////////////////////////////////////////////////

	/**
	 * Скрывает при выводе и редактировании указанные колонки
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colum Колонка или массив колонок, которые нужно скрыть
	 * @param boolean $hide true - скрыть<br>false - показать<br>
	 * по умолчанию показываются все кромя первичного ключа при редактировании
	 * @return ScaffoldController
	 */
	protected function hide($colum, $hide=true)
	{
		return $this->setColumOpt('hide', $colum, $hide);
	}

	/**
	 * Устанавливает фильтр для поля
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colum Колонка<br>
	 * Или массив ключи - колонки, значения параметры фильтра
	 * @param mixed|true|false $filter параметр фильтра<br>
	 * false - выключен<br>
	 * true - включен по умолчанию<br>
	 * mixed - произвольный параметр
	 * @return ScaffoldController
	 */
	protected function filter($colum, $filter=true)
	{
		return $this->setColumOpt('filter', $colum, $filter);
	}

	/**
	 * Устанавливает описание для поля
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colum Колонка<br>
	 * Или массив ключи - колонки, значения описания
	 * @param string $desc описание
	 * @return ScaffoldController
	 */
	protected function desc($colum, $desc='')
	{
		return $this->setColumOpt('desc', $colum, $desc);
	}

	/**
	 * Устанавливает заголовки для столбцов
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colum Колонка<br>
	 * Или массив ключи - колонки, значения заголовки
	 * @param string $title Заголовок
	 * @return ScaffoldController
	 */
	protected function title($colum, $title='')
	{
		return $this->setColumOpt('title', $colum, $title);
	}

	/**
	 * Устанавливает флаг, что поле обязательно для заполнения
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colum Колонка<br>
	 * Или массив ключи - колонки, значения заголовки
	 * @param string $title Заголовок
	 * @return ScaffoldController
	 */
	protected function required($colum, $required=true)
	{
		return $this->setColumOpt('required', $colum, $required);
	}

	/**
	 * Устанавливает css класс для столбца
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colum Колонка<br>
	 * Или массив ключи - колонки, значения заголовки
	 * @param bool|string $class имя класса или true (col_$key)
	 * @return ScaffoldController
	 */
	protected function setClass($colum, $class='')
	{
		return $this->setColumOpt('class', $colum, $class);
	}

	/**
	 * Принудительно устанавливает класс для поля
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string $colum Колонка
	 * @param string $className Имя класса без префикса
	 * @param mixed $param Второй параметр конструктора класса
	 * @return ScaffoldController
	 */
	protected function type($colum, $className='', $param=false)
	{
		$this->endTest();
		$c = $this->getInfoClass($colum);
		$c->type = $className;
		$c->typeParams = $param;
		return $this;
	}

	////////////////////////////////////////////////////////////
	//Закрытые функции
	////////////////////////////////////////////////////////////

	/**
	 * Устанавливает что-то для столбцов
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colum Колонка<br>
	 * Или массив ключи - колонки => значения
	 * @param string $value значение
	 * @return ScaffoldController
	 */
	private function setColumOpt($name, $colum, $value='')
	{
		$this->endTest();
		if (!is_array($colum))
			$colum = array($colum => $value);
		foreach ($colum as $col=>$val)
			$this->getInfoClass($col)->$name = $val;
		return $this;
	}

	/**
	 * Возвращает ссылку на класс заданного поля
	 *
	 * @param string $name имя поля
	 * @return Scaffold_Field_Info инфо о классе
	 */
	private function getInfoClass($name)
	{
		if (empty($this->fields[$name]))
			$this->fields[$name] = new Scaffold_Field_Info();
		return $this->fields[$name];
	}


	/**
	 * Получает старые значения для записи или дефолтовые для новой
	 *
	 * @param integer $id ID записи
	 * @return array значения
	 */
	private function getOldVars($id)
	{
		if ($id != -1)
			return QFW::$db->selectRow('SELECT ?# FROM ?# WHERE ?#=?',
				array($this->table=>array_merge($this->order, array('*'))),
				$this->table, $this->primaryKey, $id);

		//получение дефолтовых значений для новой записи
		$data = array();
		$fields = array();
		//сортированные поля
		foreach($this->order as $f)
			$fields[] = $f;
		//остальные поля
		foreach ($this->fields as $f=>$info)
		if (!isset($fields[$f]))
			$fields[] = $f;
		//вынимаем с учетом default_*
		foreach($fields as $f)
			if (isset($this->methods['default_'.ucfirst($f)]))
				$data[$f] = call_user_func(array(get_class($this), 'default_'.ucfirst($f)));
			else
				$data[$f] = $this->fields[$f]->def();
		return $data;
	}

	/**
	 * Генерирует фильтр для запроса
	 *
	 * @return array(<br>
	 *  'where' => DbSimple_SubQuery сгенерерованное условие с учетом фильтров<br>
	 *  'form' => array инпуты формы<br>
	 * );
	 */
	private function filterGen()
	{
		$where = array();
		$form = array();
		foreach($this->fields as $name => $field)
		{
			if (!$field->filter)
				continue;
			$data = !empty($this->sess['filter'][$name]) ?
				$this->sess['filter'][$name] : false;

			$form[$name] = $field->filterForm($data);
			if ($data === false)
				continue;
			$where[$name] = $field->filterWhere($this->sess['filter'][$name]);
		}
		if (count($where) == 0)
			return array(
				'where' => QFW::$db->subquery('1'),
				'form' => $form,
			);
		$s = '1'.str_repeat(' AND ?s', count($where));
		$args = array_merge(array($s), array_values($where));
		return array(
			'where' => call_user_func_array(array(QFW::$db, 'subquery'), $args),
			'form' => $form,
		);
	}

	/**
	 * Устанавливает порядок сортировки
	 *
	 * @param string $field Имя поля
	 * @param string $dir Направление сортировки (ASC|DESC|) - пустое - сменить
	 * @return bool Удачно или нет
	 */
	public function setSort($field='', $dir='')
	{
		//такого поля нету
		if (!isset($this->fields[$field]))
			return false;
		//если сортировки в этой таблице еще нет
		if (!isset($this->sess['sort']))
			$this->sess['sort'] = array(
				'field' => '',
				'direction' => '',
			);
		//если не указана, то ASC или сменить ASC на DESC
		if ($dir != 'ASC' && $dir != 'DESC')
			$dir = ($this->sess['sort']['field'] == $field &&
				$this->sess['sort']['direction'] == 'ASC')
				 ? 'DESC' : 'ASC';
		$this->sess['sort'] = array(
			'field' => $field,
			'direction' => $dir,
		);
		return true;

	}

	/**
	 * Генерирует сортировку
	 *
	 * @return DbSimple_SubQuery Подзапрос сортировки
	 */
	private function getSort()
	{
		if (!isset($this->sess['sort']))
			return DBSIMPLE_SKIP;
		$order = $this->sess['sort'];
		QFW::$view->assign('order', $order);
		return QFW::$db->subquery('order by ?# '.$order['direction'],
			array($this->table => $order['field']));
	}

	/**
	 * Получает части запроса для связанных полей
	 *
	 * @return array два объекта subQuery - список полей и список join
	 */
	private function getForeign()
	{
		$foreign = array();
		foreach ($this->fields as $f=>$info)
		{
			if (!$info->foreign)
				continue;
			$foreign['field'][$f] = QFW::$db->subquery('?# AS ?#', array(
				$f.'_table' => $info->foreign['field']),
				$f);
			$foreign['join'][$f] = QFW::$db->subquery('LEFT JOIN ?# AS ?# ON ?# = ?#',
				$info->foreign['table'],
				$f.'_table',
				array($f.'_table' => $info->foreign['key']),
				array($this->table => $f)
			);
		}
		if (isset($foreign['field']))
		{
			$s = str_repeat(', ?s', count($foreign['field']));
			$args = array_merge(array($s), array_values($foreign['field']));
			$foreign['field'] = call_user_func_array(array(QFW::$db, 'subquery'), $args);
		} else $foreign['field'] = DBSIMPLE_SKIP;
		if (isset($foreign['join']))
		{
			$s = str_repeat("\n ?s", count($foreign['join']));
			$args = array_merge(array($s), array_values($foreign['join']));
			$foreign['join'] = call_user_func_array(array(QFW::$db, 'subquery'), $args);
		} else $foreign['join'] = DBSIMPLE_SKIP;
		return $foreign;
	}

	/**
	 * Фабрика объектов полей
	 *
	 * @param Scaffold_Field_Info $infoClass Информация указанная пользователем
	 * @param array $fieldInfo Информация о поле из базы данных
	 * @return Scaffold_Field Класс поля
	 */
	private function getFieldClass($infoClass, $fieldInfo)
	{
		$infoClass->fiendInfo = $fieldInfo;
		$infoClass->table = $this->table;
		$infoClass->tableClass = get_class($this);
		$infoClass->primaryKey = $this->primaryKey;

		if ($infoClass->type)
		{
			$class = 'Scaffold_'.ucfirst($infoClass->type);
			return new $class($infoClass);
		}

		//определяем по типам и прочей известной информации
		if ($infoClass->foreign)
			return new Scaffold_Foreign($infoClass);

		$match = array();
		if (preg_match('#(.*?)(?:\((.+?)\)|$)#', $fieldInfo['Type'], $match))
			if (class_exists($class = 'Scaffold_'.ucfirst($match[1])))
				return new $class($infoClass, isset($match[2]) ? $match[2] : false );

		return new Scaffold_Field($infoClass);
	}

	private function endTest()
	{
		if ($this->setup)
			throw new Exception('Ты что творишь, ща руки оторву. Я же уже все данные извлек.', 1);
	}

}
?>
