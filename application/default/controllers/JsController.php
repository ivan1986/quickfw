<?php
/**
 * Выполнение php в js
 *
 * @author ivan1986
 */
class JsController
{
	/** Путь к папке с js - проецируется на DOC_ROOT/js */
	private $path;

	public function __construct()
	{
		//$this->path = DOC_ROOT.'/js';
		$this->path = APPPATH.'/_common/js';
	}

	/**
	 * Генерирует несуществующую js из php шаблона
	 *
	 * @return string сгенерированная js
	 */
	public function indexAction()
	{
		QFW::$view->mainTemplate = '';
		$args = func_get_args();
		$js = implode('/', $args);
		if (!is_file($this->path.'/'.$js))
			QFW::$router->show404();

		header('Content-Type: application/javascript');
		QFW::$view->setScriptPath($this->path);
		$text = QFW::$view->fetch($js);

		return $text;
	}

	/**
	 * Генерирует все файлы js
	 */
	public function genCli()
	{
		$out = array();
		$ret = false;
		chdir($this->path);
		QFW::$view->setScriptPath($this->path);
		exec('find . -name \'*.js\'', $out, $ret);
		if ($ret)
			return;
		foreach ($out as $file)
			file_put_contents(DOC_ROOT.'/js/'.$file, QFW::$view->fetch($file));
	}

	/**
	 * Удаляет автоматически сгенерированные js
	 */
	public function cleanCli()
	{
		$out = array();
		$ret = false;
		chdir($this->path);
		exec('find . -name \'*.js\'', $out, $ret);
		if ($ret)
			return;
		foreach ($out as $file)
		{
			$js = str_replace('./', DOC_ROOT.'/js/', $file);
			if (is_file($js))
				unlink($js);
		}
	}

}

?>
