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

	public function indexAction()
	{
		//пока так
		return $this->allAction();
	}

	/**
	 * Генерирует общую страницу с документацией
	 */
	public function allAction()
	{
		$file = str_replace(QFW::$router->UriPath, '', QFW::$router->Uri);
		if ($file == '/')
			$file = '/quickfw.html';
		$file = TMPPATH.'/doc'.$file;
		//условие для стилей
		if (strpos($file, '.css')!==false)
			header('Content-Type: text/css');
		return file_get_contents($file);
	}

}


?>
