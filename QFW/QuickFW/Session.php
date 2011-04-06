<?php

/**
 * Класс для работы с сессиями
 *
 * @package QFW
 */
class QuickFW_Session
{
	/** @var Cacher_File кешер, если включены сессии в кеше */
	private static $cache;

	/**
	 * Нужна для session_set_save_handler
	 *
	 * @internal
	 * @return boolean всегда true
	 */
	public static function close()
	{
		return true;
	}

	/**
	 * Нужна для session_set_save_handler
	 *
	 * @internal
	 * @return boolean всегда true
	 */
	public static function open()
	{
		return true;
	}

	/**
	 * Чтение сессии для session_set_save_handler
	 *
	 * @internal
	 * @return mixed
	 */
	public static function read($id)
	{
		$data = self::$cache->load('sess_'.$id);
		if (!$data)
			return false;
		$_SESSION = $data;
		return $data;
	}

	/**
	 * Запись сессии для session_set_save_handler
	 *
	 * @internal
	 * @param string $id идентификатор сесии
	 * @param string $data данные сесии
	 */
	public static function write($id,$data)
	{
		if (!empty($_SESSION))
			self::$cache->save($_SESSION, 'sess_'.$id);
		else
			self::$cache->remove('sess_'.$id);
	}

	/**
	 * Уничтожение сессии
	 */
	public static function destroy()
	{
		if (!session_id())
			return;
		self::dest(session_id());
		session_destroy();
	}

	/**
	 * Уничтожение сесии для session_set_save_handler
	 *
	 * @param string $id идентификатор сесии
	 */
	private static function dest($id)
	{
		setcookie(session_name(), '', 1, '/',
			isset(QFW::$config['session']['domain']) ? QFW::$config['session']['domain'] : '');
		unset($_COOKIE[session_name()]);
		if (self::$cache)
			self::$cache->remove('sess_'.$id);
		$_SESSION = array();
	}

	/**
	 * Очистка мусора для session_set_save_handler
	 *
	 * @return boolean всегда true
	 */
	public static function gc()
	{
		//WARNING: На сильно нагруженных системах лучше делать очистку отдельно
		//self::$cache->clean(CACHE_CLR_OLD);
		return true;
	}

	/**
	 * Отдает данные сессии без ее старта
	 *
	 * @param string $id - идентификатор сессии
	 * @param bool $update - обновить время доступа
	 * @return mixed данные сесии
	 */
	public static function get($id, $update = true)
	{
		$data = self::$cache->load('sess_'.$id);
		if (!$data)
			return false;
		if ($update)
			self::$cache->save($data, 'sess_'.$id);
		return $data;
	}
	
	/**
	 * Стартует новую сессию с новым сидом
	 * Старая(активная) полностью уничтожается
	 *
	 * @param string $sid идентификатор сессии
	 */
	public function restart($sid = '')
	{
		if ($sid==session_id())
			return;
		session_destroy();
		if (!empty($sid))
			session_id($sid);
		else
			session_regenerate_id();
		$this->start();
	}

	/**
	 * Инициализация кешера для сессий и настройки кук
	 *
	 * @param string $sid идентификатор сессии
	 */
	public function __construct($sid = '')
	{
		call_user_func_array('session_set_cookie_params', QFW::$config['session']->toArray());
		if (!empty($sid))
			session_id($sid);
		$this->start();
	}

	/**
	 * Старт сессии
	 *
	 * <br>Вызывается при наличие $_REQUEST[sessin_name()]
	 * или при рестарте сессии
	 * <br>функция нужна для инициализации обработчиков сессий
	 */
	private function start()
	{
		if (QFW::$config['QFW']['cacheSessions'])
		{
			self::$cache = Cache::get();
			session_set_save_handler(
				array('QuickFW_Session','open'),
				array('QuickFW_Session','close'),
				array('QuickFW_Session','read'),
				array('QuickFW_Session','write'),
				array('QuickFW_Session','dest'),
				array('QuickFW_Session','gc')
			);
		}
		session_start();
	}
	
}

?>