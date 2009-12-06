<?php

require_once LIBPATH.'/Modules/Scafold/Fields.php';

/**
 * Класс для быстрого создания CRUD интерфейса к таблице
 * 
 * <br><br>По умолчанию наследует себя от класса Controller
 * базового класса, который есть в этом модули
 * для того чтобы корректно подхватывать авторизацию и прочее.
 * <br><br>Класс предназначен для использования в админках
 * преймущественно для редакторивания справочников и
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
		$this->setup = true;
		parent::__construct();
		$this->ControllerUrl = QFW::$router->module.'/'.QFW::$router->controller;
		$this->methods = array_flip(get_class_methods($this));

		//Получаем данные о полях
		$fields = QFW::$db->select('SHOW FIELDS IN ?#', $this->table);
		//делаем два цикла, иначе данные о ключе не попадут в классы полей
		foreach($fields as $field)
			if ($field['Key'] == 'PRI')
			{
				$this->primaryKey = $field['Field'];
				break;
			}
		foreach($fields as $field)
			$this->fields[$field['Field']]['class'] = $this->getFieldClass($field);

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
		$count = QFW::$db->selectCell('SELECT count(*) FROM ?# '.$this->where, $this->table);

		$foregen = $this->getForegen();
		$data = QFW::$db->select('SELECT ?# { ?s } FROM ?# { ?s }'.$this->where.' LIMIT ?d, ?d',
			array($this->table=>'*'),
			isset($foregen['field']) ? $foregen['field'] : DBSIMPLE_SKIP,
			$this->table,
			isset($foregen['join']) ? $foregen['join'] : DBSIMPLE_SKIP,
			$page*$this->pageSize, $this->pageSize);

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
					$res = call_user_func(array($this, 'validator_'.ucfirst($k)), $id, $v);
				else
					$res = $this->fields[$k]['class']->validator($id, $v);
				if ($res !== true)
					$errors[$k] = $res;
			}
			//Если ошибок нет, то записываем в базу изменения
			if (count($errors) == 0)
			{
				//Обработка данных после POST
				foreach ($data as $k=>$v)
					if (isset($this->methods['proccess_'.ucfirst($k)]))
						$data[$k] = call_user_func(array($this, 'proccess_'.ucfirst($k)), $id, $v);
					else
						$data[$k] = $this->fields[$k]['class']->proccess($id, $v);

				if ($id == -1)
					QFW::$db->query('INSERT INTO ?#(?#) VALUES(?a)',
						$this->table, array_keys($data), array_values($data));
				else
					QFW::$db->query('UPDATE ?# SET ?a WHERE ?#=?',
						$this->table, $data, $this->primaryKey, $id);

				//редирект назад
				if (!empty($_SESSION['scafold_return']))
				{
					$url = $_SESSION['scafold_return'];
					unset($_SESSION['scafold_return']);
					QFW::$router->redirect($url);
				}
				else
					QFW::$router->redirect('/'.$this->ControllerUrl.'/index/');

			}
		}
		
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'index'))
			$_SESSION['scafold_return'] = $_SERVER['HTTP_REFERER'];

		if ($id == -1)
		{
			//получение дефолтовых значений для новой записи
			$data = array();
			foreach ($this->fields as $f=>$info)
				$data[$f] = $info['class']->def();
		}
		else
			$data = QFW::$db->selectRow('SELECT * FROM ?# WHERE ?#=?',
				$this->table, $this->primaryKey, $id);

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
			$v['class']->proccess($id, false);
		QFW::$db->query('DELETE FROM ?# WHERE ?#=?',
			$this->table, $this->primaryKey, $id);
		QFW::$router->redirect('/'.$this->ControllerUrl.'/index', true);
	}

	public function fieldAction($name, $id)
	{
		$args = func_get_args();
		array_shift($args);
		if (isset($this->fields[$name]))
			call_user_func_array(array($this->fields[$name]['class'], 'action'), $args);
		QFW::$router->redirect('/'.$this->ControllerUrl.'/index', true);
	}

	////////////////////////////////////////////////////////////
	//Функции для упращения настройки таблицы - удобные сеттеры
	////////////////////////////////////////////////////////////

	/**
	 * Скрывает при выводе и редактировании указанные колонки
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colums Колонка или массив колонок, которые нужно скрыть
	 * @param boolean $hide true - скрыть<br>false - показать<br>
	 * по умолчанию показываются все кромя первичного ключа при редактировании
	 * @return ScafoldController
	 */
	protected function hide($colums, $hide=true)
	{
		$this->endTest();
		if (!is_array($colums))
			$colums = array($colums);
		foreach ($colums as $col)
			$this->fields[$col]['hide'] = $hide;
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
	 * @return &ScafoldController
	 */
	protected function foregen($colum, $table, $id, $name)
	{
		$this->endTest();
		$this->fields[$colum]['foregen'] = array(
			'field' => $name,
			'table' => $table,
			'key'   => $id,
		);
		return $this;
	}

	/**
	 * Устанавливает заголовки для столбцов
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string|array $colum Колонка<br>
	 * Или массив ключи - колонки, значения заголовки
	 * @param string $title Заголовок
	 * @return &ScafoldController
	 */
	protected function title($colum, $title='')
	{
		$this->endTest();
		if (!is_array($colum))
			$colum = array($colum => $title);
		foreach ($colum as $col=>$tit)
			$this->fields[$col]['title'] = $tit;
		return $this;
	}

	/**
	 * Принудительно устанавливает класс для поля
	 *
	 * <br><br> Вызывается только в конструкторе
	 *
	 * @param string $colum Колонка
	 * @param string $className Имя класса без префикса
	 * @param mixed $param Второй параметр конструктора класса
	 * @return &ScafoldController
	 */
	protected function type($colum, $className='', $param=false)
	{
		$this->endTest();
		$this->fields[$colum]['type'] = array(
			'class' => 'Scafold_'.$className,
			'param' => $param,
		);
		return $this;
	}

	////////////////////////////////////////////////////////////
	//Закрытые функции
	////////////////////////////////////////////////////////////

	/**
	 * Получает части запроса для связанных полей
	 *
	 * @return array два объекта subQuery - список полей и список join
	 */
	private function getForegen()
	{
		$foregen = array();
		foreach ($this->fields as $f=>$info)
		{
			if (!isset($info['foregen']))
				continue;
			$foregen['field'][$f] = QFW::$db->subquery('?# AS ?#', array(
				$f.'_table' => $info['foregen']['field']),
				$f);
			$foregen['join'][$f] = QFW::$db->subquery('LEFT JOIN ?# AS ?# ON ?# = ?#',
				$info['foregen']['table'],
				$f.'_table',
				array($f.'_table' => $info['foregen']['key']),
				array($this->table => $f)
			);
		}
		if (isset($foregen['field']))
		{
			$s = str_repeat(', ?s', count($foregen['field']));
			$args = array_values($foregen['field']);
			$args = array_merge(array($s), $args);
			$foregen['field'] = call_user_func_array(array(QFW::$db, 'subquery'), $args);
		}
		if (isset($foregen['join']))
		{
			$s = str_repeat("\n ?s", count($foregen['join']));
			$args = array_values($foregen['join']);
			$args = array_merge(array($s), $args);
			$foregen['join'] = call_user_func_array(array(QFW::$db, 'subquery'), $args);
		}
		return $foregen;
	}

	/**
	 * Фабрика объектов полей
	 *
	 * @param array $fieldInfo Информация о поле из базы данных
	 * @return Scafold_Field Класс поля
	 */
	private function getFieldClass($fieldInfo)
	{
		$info = array(
			'table' => $this->table,
			'primaryKey' => $this->primaryKey,
			'base' => $fieldInfo,
			'field' => isset($this->fields[$fieldInfo['Field']]) ?
				$this->fields[$fieldInfo['Field']] : false,
		);

		if (isset($info['field']['type']))
			return new $info['field']['type']['class']($info, $info['field']['type']['param']);

		//определяем по типам и прочей известной информации
		if (isset($this->fields[$fieldInfo['Field']]['foregen']))
			return new Scafold_Foregen($info);

		return new Scafold_Field($info);
	}

	private function endTest()
	{
		if ($this->setup)
			throw new Exception('Ты что творишь, ща руки оторву. Я же уже все данные извлек.', 1);
	}

}
?>
