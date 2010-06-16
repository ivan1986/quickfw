<?php
/**
 * Преобразование scss в css
 *
 * <br>Необходимо установить sass версии не ниже 3.0
 *
 * @author ivan1986
 */
class CssController
{
	/** Путь к sass */
	const SASS = 'sass -C';

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
		if (!is_file(DOC_ROOT.'/css/'.$scss))
			QFW::$router->show404();

		header('Content-Type: text/css');
		$out = array();
		$ret = false;
		//запускаем преобразования - по умолчанию без кеша и выводим ошибки в основной поток
		exec(self::SASS.' 2>&1 '.DOC_ROOT.'/css/'.$scss, $out, $ret);
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
		exec('find '.DOC_ROOT.'/css -name \'*.scss\'', $out, $ret);
		if ($ret)
			return;
		foreach ($out as $file)
		{
			$css = str_replace('.scss', '.css', $file);
			exec(self::SASS.' 2>&1 '.$file.' | unexpand -t2 --first-only >'.$css, $out, $ret);
		}
	}

	/**
	 * Удаляет автоматически сгенерированные css
	 */
	public function cleanCli()
	{
		$out = array();
		$ret = false;
		exec('find '.DOC_ROOT.'/css -name \'*.scss\'', $out, $ret);
		if ($ret)
			return;
		foreach ($out as $file)
		{
			$css = str_replace('.scss', '.css', $file);
			if (is_file($css))
				unlink($css);
		}
	}

}

?>
