<?php

require 'Controller.php';
require LIBPATH.'/Modules/Scafold/ScafoldController.php';
/**
 * Description of TableController
 *
 * @author ivan
 */
class TableController extends ScafoldController
{
    public function __construct()
	{
		QFW::$view->mainTemplate = '';
		QFW::$db = new DbSimple_Connect('mypdo://root@localhost/test?enc=utf8');
		$this->table = '?_posts';

		$this->hide('id');
		$this->foreign('user_id', '?_users', 'id', 'name');
		$this->foreign('modered_by', '?_users', 'id', 'name');
		$this->title('user_id', 'Пользователь')->title(array(
			'modered_by' => 'Отмодерировал',
			'text' => 'Сообщение',
			'file' => 'Файл',
		));
		$this->filter('text', true);
		$this->filter('char', true);
		$this->type('text', 'Text', array(
			'cols' => 120,
			'trim' => 10,
		));
		$this->type('file', 'File', array(
			'path' => DOC_ROOT.'/upload',
		));

		parent::__construct();
	}

	public function indexAction($page=1)
	{
		$data = parent::indexAction($page);

		$menu = QFW::$router->blockRoute('helper.nav.menu', array(
			'Пункт 1' => 'admin',
			'Пункт 2' => 'default/index/index',
			'Пункт 3' => 'default/table/index',
		), 'zzz', false);

		return $menu.'<br />'.$data;

	}

}
?>
