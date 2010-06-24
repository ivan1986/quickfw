<?php

/**
 * Перегенерирует и отображает документацию
 *
 */
class DocController
{
	public function __construct()
	{
		QFW::$view->mainTemplate='';
		//Обновим документацию - use the Make, Luke
		exec('cd '.ROOTPATH.'/doc/asciidoc && make site');
		exec('chmod -R 777 '.TMPPATH.'/doc');
	}

	/**
	 * Генерирует общую страницу с документацией
	 */
	public function indexAction()
	{
		$args = func_get_args();
		if (substr($_SERVER['REQUEST_URI'], -1, 1) != '/' && count($args) == 0)
			QFW::$router->redirect(Url::A());
		$file = implode('/', $args);
		if ($file == '')
			$file = 'quickfw.html';
		$file = TMPPATH.'/doc/'.$file;
		//условие для стилей
		if (strpos($file, '.css')!==false)
			header('Content-Type: text/css');
		return file_get_contents($file);
	}

}


?>
