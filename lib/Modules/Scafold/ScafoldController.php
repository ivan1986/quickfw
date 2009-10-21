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
	/** @var integer Количество элементов на странице */
	protected $pageSize = 20;
	/** @var string Имя первичного ключа таблицы */
	protected $primaryKey = 'id';
	/** @var array Зависимых ключей */
	protected $foregen = array();

	/** @var string Адрес контроллера */
	private $ControllerUrl;
	/** @var array Массив методов */
	private $methods;

	public function  __construct()
	{
		parent::__construct();
		$this->ControllerUrl = QFW::$router->module.'/'.QFW::$router->controller;
		$this->methods = array_flip(get_class_methods($this));
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
		$count = QFW::$db->selectCell('SELECT count(*) FROM ?#', $this->table);
		$data = QFW::$db->select('SELECT * FROM ?# LIMIT ?d, ?d', 
			$this->table, $page*$this->pageSize, $this->pageSize);

		//получаем пагинатор
		$curUrl = QFW::$view->P->siteUrl($this->ControllerUrl.'/index/$');
		$pages = ceil($count/$this->pageSize);
		$pager=QFW::$router->blockRoute('nav.pager('.$curUrl.','.$pages.','.($page+1).')');

		return QFW::$view->assign(array(
			'data' => $data,
			'pager' => $pager,
			'info' => array(
				'ControllerUrl' => $this->ControllerUrl,
				'primaryKey' => $this->primaryKey,
			),
			'methods' => $this->methods,
			'class' => get_class($this),
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

		$data = QFW::$db->selectRow('SELECT * FROM ?# {WHERE ?#=?|LIMIT 1}',
			$this->table, $this->primaryKey, $id==-1 ? DBSIMPLE_SKIP : $id);
		if ($id == -1)
		{
			if (count($data == 0))
			{
				$data = QFW::$db->selectCol('SHOW FIELDS IN ?#', $this->table);
				$data = array_flip($data);
			}
			foreach ($data as $k=>$v)
				$data[$k]='';
		}

		return QFW::$view->assign(array(
			'id' => $id,
			'data' => $data,
			'info' => array(
				'ControllerUrl' => $this->ControllerUrl,
				'primaryKey' => $this->primaryKey,
			),
			'methods' => $this->methods,
			'class' => get_class($this),
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
		QFW::$db->query('DELETE FROM ?# WHERE ?#=?',
			$this->table, $this->primaryKey, $id);
		QFW::$router->redirect($this->ControllerUrl.'/index', true);
	}

}
?>
