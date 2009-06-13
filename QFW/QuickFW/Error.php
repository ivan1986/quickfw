<?php

	/**
	 * Функция - обработчик ошибок
	 * На продакшене записывает в лог
	 * На тестовом вылетает как эксепшн
	 */
	function exception_error_handler($errno, $errstr, $errfile, $errline )
	{
		if (QFW::$config['QFW']['release'])
		{
			require_once LIBPATH.'/Log.php';
			Log::log($errstr.' in '.$errfile.' on line '.$errline,'debug');
			return false;
		}
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	set_error_handler("exception_error_handler");

	/**
	 * Обработка исключений
	 * Обрабатывает исключение неавторизованности как нормальное - показом контента
	 * Исключение 404 - показывает ошибку 404
	 * Остальные - на продакшене 404 и в лог
	 * в дебаге - на страницу
	 */
	function exception_handler(Exception $exception)
	{
		$GLOBALS['DONE'] = 1;
		if ($exception instanceof AuthException)
			echo $exception->getMessage();
		elseif ($exception instanceof S404Exception)
			QFW::$router->show404();
		elseif (QFW::$config['QFW']['release'])
		{
			require_once LIBPATH.'/Log.php';
			Log::log("Uncaught exception: " . $exception->getMessage(),'debug');
			QFW::$router->show404();
		}
		else
			echo "Uncaught exception: " , $exception->getMessage(), "\n";
	}
	set_exception_handler('exception_handler');

	/**
	 * Классы исключений
	 *
	 */
	class AuthException extends Exception {}
	class S404Exception extends Exception {}

	/**
	 * Перехватчик фатальных ошибок
	 * В случае фатальной ошибки может что-то сделать
	 * TODO: Дописать коммент
	 */
	function FatalErrorHandler($text)
	{
		//тут определим что жопа не случилась
		if ($GLOBALS['DONE'])
			return false;
		// Случилась жопа, начинаем обработку ошибок
		$GLOBALS['config'] = QFW::config();
		QFW::Init();
		require_once LIBPATH.'/Log.php';
		Log::log("Fatal Error" ,'critical');
		//TODO: Выдирать лог ошибок и отправлять последние куда-то
		return $text;
	}
	ob_start('FatalErrorHandler');

?>