<?php

	function exception_error_handler($errno, $errstr, $errfile, $errline )
	{
		echo $errline.' '.$errfile.' '.$errstr;
		return false;
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	set_error_handler("exception_error_handler");
	
	function exception_handler($exception)
	{
		$GLOBALS['DONE'] = 1;
		echo "Uncaught exception: " , $exception->getMessage(), "\n";
	}
	set_exception_handler('exception_handler');
	
	function FatalErrorHandler($text)
	{
		//тут определим что жопа не случилась
		if ($GLOBALS['DONE'])
			return false;
		/**
		 * Случилась жопа, начинаем обработку ошибок
		 */
		require QFWPATH.'/config.php';
		require APPPATH.'/default.php';
		if (isset($_SERVER['HTTP_HOST']))
		{
			$file=APPPATH.'/'.$_SERVER['HTTP_HOST'].'.php';
			if (is_file($file))
				require ($file);
		}
		$GLOBALS['config']=$config;
		QFW::Init();
		require_once LIBPATH.'/Log.php';
		$err = file_get_contents('/home/ivan/QuickFramework/error.log');
		Log::log($err);
		//file_put_contents(TMPPATH.'/2',serialize($GLOBALS));
		return $text.'CALL';
	}
	ob_start('FatalErrorHandler');

?>