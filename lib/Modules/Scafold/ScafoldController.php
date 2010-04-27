<?php

require_once LIBPATH.'/Modules/Scafold/Fields.php';

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
 * <br>require LIBPATH.'/Modules/Scafold/ScafoldController.php';
 * <br>.....
 * <br>class TableController extends ScafoldController
 *
 * @author Ivan1986
 */
abstract class ScafoldController extends Controller
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

	/** @var string Адрес контроллера */
	private $ControllerUrl;
	/** @var array Массив методов */
	private $methods;
	/** @var boolean Флаг окончания настройки */
	private $setup = false;

	/**
	 * Конструктор вызывать только после настройки таблицы
	 */
	public function  __construct()
	{
		QFW::$view->P->addCSS('css/buildin/scafold.css');
		$this->setup = true;
		parent::__construct();
		$this->ControllerUrl = QFW::$router->module.'/'.QFW::$router->controller;
		$this->methods = array_flip(get_class_methods($this));

		//Получаем данные о полях
		$fields = QFW::$db->select('SHOW FIELDS IN ?#', $this->table);
		foreach($fields as $field)
		{
			$c = $this->getInfoClass($field['Field']);
			$c->primaryKey = $field['Key'] == 'PRI';
			$this->fields[$field['Field']] = 
				$this->getFieldClass($c, $field);
		}
		foreach($this->fields as $k=>$field)
			if (get_class($field) == 'Scafold_Field_Info')
				unset($this->fields[$k]);

		//Общая информация о таблице
		QFW::$view->assign(array(
			'methods' => $this->methods,
			'class' => get_class($this),
			'info' => array(
				'ControllerUrl' => $this->ControllerUrl,
				'primaryKey' => $this->primaryKey,
			),
			'fields' => $this->fields,
			'actions' => $this->actions,
			'table' => str_replace('?_', '', $this->table),
		));
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
			$this->session();
			if (isset($_POST['parent']))
			{
				$_SESSION['scafold'][$this->table]['parent'] = $_POST['parent'];
				QFW::$router->redirectMCA(QFW::$router->module.'/'.QFW::$router->controller.'/index');
			}
			if (empty($_SESSION['scafold'][$this->table]['parent']))
				$_SESSION['scafold'][$this->table]['parent'] = count($parent) ? key($parent) : 0;

			QFW::$view->assign('parent', QFW::$view->assign('parent', array(
				'list' => $parent,
				'current' => $_SESSION['scafold'][$this->table]['parent'],
			))->fetch('scafold/parent.html'));
			$parentWhere = QFW::$db->subquery('AND ?#=?',
					array($this->table => $this->parentData['colum']),
					$_SESSION['scafold'][$this->table]['parent']);
		}

		$filter = $this->filterGen();
		$count = QFW::$db->selectCell('SELECT count(*) FROM ?# 
			WHERE ?s ?s '.$this->where, $this->table, $filter['where'], $parentWhere);

		$foreign = $this->getForeign();
		$data = QFW::$db->select('SELECT ?# ?s FROM ?# ?s
			WHERE ?s ?s '.$this->where.' LIMIT ?d, ?d',
			array($this->table=>'*'),
			$foreign['field'], $this->table, $foreign['join'],
			$filter['where'], $parentWhere,
			$page*$this->pageSize, $this->pageSize);

		if (count($filter['form']))
		{
			require_once LIBPATH.'/MetaForm/FormPersister.php';
			ob_start(array(new HTML_FormPersister(), 'process'));
			QFW::$view->assign('filter', $filter['form']);
		}
		//получаем пагинатор
		$curUrl = QFW::$view->P->siteUrl($this->ControllerUrl.'/index/$');
		$pages = ceil($count/$this->pageSize);
		$pager=QFW::$router->blockRoute('helper.nav.pager('.$curUrl.','.$pages.','.($page+1).')');

		return QFW::$view->assign(array(
			'data' => $data,
			'pager' => $pager,
		))->fetch('scafold/index.html');
	}

	/**
	 * Отображает форму редактирования или добавления
	 *
	 * @param int $id ключ записи для редактирования
	 */
	public function editAction($id=-1)
	{
		//инициализация FormPersister
		require_once LIBPATH.'/MetaForm/FormPersister.php';
		ob_start(array(new HTML_FormPersister(), 'process'));
		$errors = array();
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($_POST['data'])>0)
		{
			//Обработка результата редактирования
			$data = $_POST['data'];
			foreach ($data as $k=>$v)
			{
				if (isset($this->methods['validator_'.ucfirst($k)]))
					$res = call_user_func(array($this, 'validator_'.ucfirst($k)), $v, $id);
				else
					$res = $this->fields[$k]->validator($id, $v);
				if ($res !== true)
					$errors[$k] = $res;
			}
			//Если ошибок нет, то записываем в базу изменения
			if (count($errors) == 0)
			{
				//Обработка данных после POST
				foreach ($this->fields as $k=>$class)
					if ($k == $this->primaryKey && !isset($data[$k]))
						continue; //не трогаем первичный ключ
					elseif (isset($this->methods['proccess_'.ucfirst($k)]))
						$data[$k] = call_user_func(array($this, 'proccess_'.ucfirst($k)), 
							isset($data[$k]) ? $data[$k] : $class->def(), $id);
					else
						$data[$k] = $class->proccess($id,
							isset($data[$k]) ? $data[$k] : $class->def());

				if ($id == -1)
					QFW::$db->query('INSERT INTO ?#(?#) VALUES(?a)',
						$this->table, array_keys($data), array_values($data));
				else
					QFW::$db->query('UPDATE ?# SET ?a WHERE ?#=?',
						$this->table, $data, $this->primaryKey, $id);

				//редирект назад
				if (!empty($_SESSION['scafold']['return']))
				{
					$url = $_SESSION['scafold']['return'];
					unset($_SESSION['scafold']['return']);
					QFW::$router->redirect($url);
				}
				else
					QFW::$router->redirect('/'.$this->ControllerUrl.'/index/');

			}
		}
		
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'index'))
			$_SESSION['scafold']['return'] = $_SERVER['HTTP_REFERER'];

		if ($id == -1)
		{
			//получение дефолтовых значений для новой записи
			$data = array();
			foreach ($this->fields as $f=>$info)
				$data[$f] = $info->def();
		}
		else
			$data = QFW::$db->selectRow('SELECT * FROM ?# WHERE ?#=?',
				$this->table, $this->primaryKey, $id);

		$state = new TemplaterState(QFW::$view);
		QFW::$view->setScriptPath(dirname(__FILE__));

		return QFW::$view->assign(array(
			'id' => $id,
			'data' => $data,
			'errors' => $errors,
		))->fetch('scafold/edit.html');
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
		foreach($this->fields as $k=>$v)
			$v->proccess($id, false);
		QFW::$db->query('DELETE FROM ?# WHERE ?#=?',
			$this->table, $this->primaryKey, $id);
		QFW::$router->redirect('/'.$this->ControllerUrl.'/index', true);
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
		QFW::$router->redirect('/'.$this->ControllerUrl.'/index', true);
	}

	/**
	 * обработка смены фильтра
	 */
	public function filterAction()
	{
		$this->session();
		if (!empty($_POST['clear']))
		{
			$_SESSION['scafold']['filter'] = array();
			QFW::$router->redirect('/'.$this->ControllerUrl.'/index', true);
		}
		if (empty($_POST['filter']) || empty($_POST['apply']))
			QFW::$router->redirect('/'.$this->ControllerUrl.'/index', true);
		$_SESSION['scafold']['filter'] = $_POST['filter'];
		
		QFW::$router->redirect('/'.$this->ControllerUrl.'/index', true);
	}

	////////////////////////////////////////////////////////////
	//Функции для упращения настройки таблицы - удобные сеттеры
	////////////////////////////////////////////////////////////

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
	 * @return ScafoldController
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
	 * @return ScafoldController
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
	 * @return ScafoldController
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
	 * @return ScafoldController
	 */
	protected function filter($colum, $filter='')
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
	 * @return ScafoldController
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
	 * @return ScafoldController
	 */
	protected function title($colum, $title='')
	{
		return $this->setColumOpt('title', $colum, $title);
	}

	/**
	 * Принудительно устанавливает класс для поля
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string $colum Колонка
	 * @param string $className Имя класса без префикса
	 * @param mixed $param Второй параметр конструктора класса
	 * @return ScafoldController
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
	 * @return ScafoldController
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
	 * @return Scafold_Field_Info инфо о классе
	 */
	private function getInfoClass($name)
	{
		if (empty($this->fields[$name]))
			$this->fields[$name] = new Scafold_Field_Info();
		return $this->fields[$name];
	}

	/**
	 * Генерирует фильтр для запроса
	 *
	 * @return array(<br>
	 *	'where' => DbSimple_SubQuery сгенерерованное условие с учетом фильтров<br>
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
			$this->session();
			$data = !empty($_SESSION['scafold'][$this->table]['filter'][$name]) ?
				$_SESSION['scafold'][$this->table]['filter'][$name] : false;

			$form[$name] = $field->filterForm($data);
			if ($data === false)
				continue;
			$where[$name] = $field->filterWhere($_SESSION['scafold'][$this->table]['filter'][$name]);
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
	 * @param Scafold_Field_Info $infoClass Информация указанная пользователем
	 * @param array $fieldInfo Информация о поле из базы данных
	 * @return Scafold_Field Класс поля
	 */
	private function getFieldClass($infoClass, $fieldInfo)
	{
		$infoClass->fiendInfo = $fieldInfo;
		$infoClass->table = $this->table;
		$infoClass->primaryKey = $this->primaryKey;

		if ($infoClass->type)
		{
			$class = 'Scafold_'.ucfirst($infoClass->type);
			return new $class($infoClass);
		}

		//определяем по типам и прочей известной информации
		if ($infoClass->foreign)
			return new Scafold_Foreign($infoClass);

		$match = array();
		if (preg_match('#(.*?)(?:\((.+?)\)|$)#', $fieldInfo['Type'], $match))
			if (class_exists($class = 'Scafold_'.ucfirst($match[1])))
				return new $class($infoClass, isset($match[2]) ? $match[2] : false );

		return new Scafold_Field($infoClass);
	}

	private function endTest()
	{
		if ($this->setup)
			throw new Exception('Ты что творишь, ща руки оторву. Я же уже все данные извлек.', 1);
	}

}
?>
