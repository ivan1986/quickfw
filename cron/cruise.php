<?php
	define ('DOC_ROOT', dirname(dirname(__FILE__)).'/www');
	define ('ROOTPATH', dirname(dirname(__FILE__)));
	define ('APPPATH', ROOTPATH . '/application');
	define ('VARPATH', ROOTPATH . '/var');
	define ('QFWPATH', ROOTPATH . '/QFW');
	define ('LIBPATH', ROOTPATH . '/lib');

//Настройки по умолчанию, если нужно - измените
//	define ('TMPPATH', VARPATH . '/tmp');
//	define ('LOGPATH', VARPATH . '/log');
//	define ('COMPATH', APPPATH . '/_common');
//	define ('MODPATH', COMPATH . '/models');

	if (empty($_SERVER['HTTP_HOST']))
		$_SERVER['HTTP_HOST'] = gethostname();

	require (QFWPATH.'/Init.php');

	QFW::$view->mainTemplate='';

	//Если нету действий, то выходим
	if (empty(QFW::$config['cruise']['actions']))
		return;

	//Проверяем на повторный запуст себя
	require_once LIBPATH.'/Lock.php';
	Lock::doubleRun('cruise_'.$_SERVER['HTTP_HOST']);

	$Cache = Cache::get('default', 'cruise_'.$_SERVER['HTTP_HOST']);
	$Cache->clean(CACHE_CLR_OLD);

	//сохраняем когда рестартовать
	if (QFW::$config['cruise']['restart'])
		$Cache->save(1, 'restart', array(), QFW::$config['cruise']['restart']);


	while(!QFW::$config['cruise']['restart'] || $Cache->load('restart'))
	{
		foreach (QFW::$config['cruise']['actions'] as $action => $time)
		{
			if ($Cache->load($action))
				continue;
			QFW::$router->route($action, 'Cli');
			$t = $time[0] + mt_rand(-$time[1], $time[1]);
			$Cache->save(1, $action, array(), $t);
		}
		sleep(QFW::$config['cruise']['interval']);
		if (class_exists('Log'))
			Log::sendQuery();
	}

?>
