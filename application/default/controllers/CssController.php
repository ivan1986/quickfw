<?php
/**
 * Преобразование scss в css
 * Выполнение php в css
 *
 * <br>Необходимо установить sass версии не ниже 3.0
 *
 * @author ivan1986
 */
class CssController
{
	/** Путь к sass */
	const SASS = 'sass --scss -C';
	/** Путь к папке с scss - проецируется на DOC_ROOT/css */
	private $path;

	public function __construct()
	{
		//$this->path = DOC_ROOT.'/css';
		$this->path = APPPATH.'/_common/css';
	}

	/**
	 * Генерирует несуществующую css по scss если он есть
	 *
	 * @return string сгенерированная css
	 */
	public function indexAction()
	{
		QFW::$view->mainTemplate = '';
		$args = func_get_args();
		$css = implode('/', $args);
		$scss = str_replace('.css', '.scss', $css);
		if (!is_file($this->path.'/'.$scss))
			QFW::$router->show404();

		header('Content-Type: text/css');
		$out = array();
		$ret = false;
		QFW::$view->setScriptPath($this->path);
		$text = QFW::$view->fetch($scss);
		//запускаем преобразования - по умолчанию без кеша и выводим ошибки в основной поток
		exec('echo '.escapeshellarg($text).' | '.self::SASS.' 2>&1 ', $out, $ret);
		$out = implode("\n", $out);

		if ($ret)
		{	//если у нас в scss ошибка, то выводим ее
			$out = str_replace('Use --trace for backtrace.', '', $out);
			$out = trim(str_replace("\n", ' ', $out));
			$out = 'body:before { content: \''.addslashes($out).'\'; }';
		}
		return $out;
	}

	/**
	 * Преобразует все файлы scss в css
	 */
	public function genCli()
	{
		$out = array();
		$ret = false;
		chdir($this->path);
		QFW::$view->setScriptPath($this->path);
		exec('find . -name \'*.scss\'', $out, $ret);
		if ($ret)
			return;
		foreach ($out as $file)
		{
			$css = str_replace('.scss', '.css', $file);
			$text = QFW::$view->fetch($file);
			exec('echo '.escapeshellarg($text).' | '.self::SASS.' 2>&1 | unexpand -t2 --first-only > '.DOC_ROOT.'/css/'.$css, $out, $ret);
		}
	}

	/**
	 * Удаляет автоматически сгенерированные css
	 */
	public function cleanCli()
	{
		$out = array();
		$ret = false;
		chdir($this->path);
		exec('find . -name \'*.scss\'', $out, $ret);
		if ($ret)
			return;
		foreach ($out as $file)
		{
			$css = str_replace(
				array('.scss', './'),
				array('.css', DOC_ROOT.'/css/'),
			$file);
			if (is_file($css))
				unlink($css);
		}
	}

}

?>
