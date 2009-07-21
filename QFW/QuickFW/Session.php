<?php

/**
 * Класс для работы с сессиями
 *
 * @package QFW
 */
class QuickFW_Session
{
	private static $cache;

	static function close()
	{
		return true;
	}

	static function open()
	{
		return true;
	}

	static function read($id)
	{
		$data = self::$cache->load('sess_'.$id);
		if (!$data)
			return false;
		$_SESSION = $data;
		return $data;
	}

	static function write($id,$data)
	{
		if (!empty($_SESSION))
			self::$cache->save($_SESSION, 'sess_'.$id);
		else
			self::$cache->remove('sess_'.$id);
	}

	static function destroy($id)
	{
		setcookie(session_name(), '', 1, '/',
			isset(QFW::$config['session']['domain']) ? QFW::$config['session']['domain'] : '');
		unset($_COOKIE[session_name()]);
		self::$cache->remove('sess_'.$id);
		$_SESSION = array();
		session_id('');
	}

	static function gc()
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
	static function get($id, $update = true)
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
	
	public function __construct($sid = '')
	{
		self::$cache = Cache::get();
		call_user_func_array('session_set_cookie_params', QFW::$config['session']);
		if (!empty($sid))
			session_id($sid);
		$this->start();
	}

	/**
	 * Старт сессии
	 *
	 * <br>Вызывается при наличие $_REQUEST[sessin_name()]
	 * <br>функция нужна для инициализации обработчиков сессий
	 */
	private function start()
	{
		session_set_save_handler(
			array('QuickFW_Session','open'),
			array('QuickFW_Session','close'),
			array('QuickFW_Session','read'),
			array('QuickFW_Session','write'),
			array('QuickFW_Session','destroy'),
			array('QuickFW_Session','gc')
		);
		session_start();
	}
	
}

?>