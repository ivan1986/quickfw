<?php

require_once LIBPATH.'/Debug/ErrorHook/Listener.php';

class QFW_Error extends Debug_ErrorHook_Listener
{
	private static $Instance;

	/**
	 * Добавляет обработчик из конфига
	 *
	 * @param array $handler данные из конфига
	 */
	public static function addFromConfig($handler)
	{
		if (!self::$Instance)
			self::$Instance = new self();;

		$name = ucfirst($handler['name']);
		require_once LIBPATH.'/Debug/ErrorHook/'.$name.'Notifier.php';
		//пока так, потом возможно придется переделать
		if ($name == 'Mail')
		{
			$i = new Debug_ErrorHook_MailNotifier(
				$handler['options']['to'], $handler['options']['whatToSend'],
				$handler['options']['subjPrefix'], $handler['options']['charset']);
		}
		else
		{
			$class = 'Debug_ErrorHook_'.$name.'Notifier';
			$i = new $class($handler['options']['whatToSend']);
		}
		if ($handler['RemoveDups'])
		{
			require_once LIBPATH.'/Debug/ErrorHook/RemoveDupsWrapper.php';
			$i = new Debug_ErrorHook_RemoveDupsWrapper($i,
				TMPPATH.'/errors', $handler['RemoveDups']);
		}
		self::$Instance->addNotifier($i);

	}
}

?>