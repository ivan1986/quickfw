<?php

require_once 'Controller.php';

/**
 *	Класс для быстрого создания CRUD интерфейса к таблице
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

	public function  __construct()
	{
		parent::__construct();
		$this->ControllerUrl = QFW::$router->module.'/'.QFW::$router->controller;
		$this->methods = array_flip(get_class_methods($this));
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
		$pager=QFW::$router->blockRoute('nav.pager('.$curUrl.','.$pages.','.($page+1).')');

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
		//инициализация метаформы
		require_once LIBPATH.'/MetaForm/FormPersister.php';
		require_once LIBPATH.'/MetaForm/MetaForm.php';
		$SemiParser = new HTML_SemiParser();
		ob_start(array(&$SemiParser, 'process'));

		$MetaForm = new HTML_MetaForm('secret_secret');
		$SemiParser->addObject($MetaForm);

		$FormPersister = new HTML_FormPersister();
		$SemiParser->addObject($FormPersister);
		$errors = array();
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			//инициализация метаформы
			require_once LIBPATH.'/MetaForm/MetaFormAction.php';
			$metaFormAction = new HTML_MetaFormAction($MetaForm);
			$metaFormAction->MFA_VALIDATOR_CLASS = get_class($this);
			if ($metaFormAction->process() == 'send')
			{
				$data = $_POST['data'];
				//Обработка данных после POST
				foreach ($data as $k=>$v)
					if (isset($this->methods['proccess_'.ucfirst($k)]))
						$data[$k] = call_user_method('proccess_'.ucfirst($k), $this, $v);
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
			else
			{	//Сохраняем ошибки
				$MFerrors = $metaFormAction->getErrors();
				foreach($MFerrors as $k=>$v)
				{
					$name = substr($v['name'], 5, -1);
					$errors[$name] = $name;
				}
			}
		}
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'index'))
			$_SESSION['scafold_return'] = $_SERVER['HTTP_REFERER'];

		if ($id == -1)
		{
			$data = array();
			$fields = QFW::$db->select('SHOW FIELDS IN ?#', $this->table);
			foreach ($fields as $v)
				$data[$v['Field']]= $v['Default'] == 'CURRENT_TIMESTAMP' ? '' : $v['Default'];
		}
		else
			$data = QFW::$db->selectRow('SELECT * FROM ?# WHERE ?#=?',
				$this->table, $this->primaryKey, $id);

		//связанные поля - строим комбобоксы
		$lookup = array();
		foreach ($this->fields as $f=>$info)
		{
			if (!isset($info['foregen']))
				continue;
			$lookup[$f] = QFW::$db->selectCol('SELECT ?# AS ARRAY_KEY_1, ?# FROM ?#',
				$info['foregen']['key'], $info['foregen']['field'], $info['foregen']['table']);
		}

		return QFW::$view->assign(array(
			'id' => $id,
			'data' => $data,
			'lookup' => $lookup,
			'errors' => $errors,
		))->fetch('scafold/edit.html');
	}

	/**
	 * Удаление строки
	 *
	 * @param int $id значение первичного ключа удаляемой строки
	 */
	public function deleteAction($id=0)
	{
		if (isset($this->methods['delete']))
		{
			$row = QFW::$db->selectRow('SELECT FROM ?# WHERE ?#=?',
				$this->table, $this->primaryKey, $id);
			if (!call_user_method('delete', $this, $row))
				QFW::$router->redirect($this->ControllerUrl.'/index', true);
		}
		QFW::$db->query('DELETE FROM ?# WHERE ?#=?',
			$this->table, $this->primaryKey, $id);
		QFW::$router->redirect($this->ControllerUrl.'/index', true);
	}

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
			$foregen['field'] = call_user_method_array('subquery', QFW::$db, $args);
		}
		if (isset($foregen['join']))
		{
			$s = str_repeat("\n ?s", count($foregen['join']));
			$args = array_values($foregen['join']);
			$args = array_merge(array($s), $args);
			$foregen['join'] = call_user_method_array('subquery', QFW::$db, $args);
		}
		return $foregen;
	}

}
?>
