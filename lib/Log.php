<?php

/**
 * Класс логирования
 *
 * @method null log() log(string $string, string $to) заносит запись в лог
 * @method null out() out(string $string, string $to) заносит запись в лог
 */
class Log
{
	private static $l=null;
	private static $messages=array();

	/**
	 * Диспетчеризация сообщения - отправляет в нужный логер или в файл
	 */
	public static function __callStatic($method, $params)
	{
		$str = $params[0];
		$to = ($method == 'log' || $method == 'out') ? (isset($params[1])?$params[1]:'general') : $method;
		if (isset(QFW::$config['log'][$to]))
			$to = QFW::$config['log'][$to];
		if (self::$l === null)
				self::$l = new Log();
		if ($to == 'error')
			error_log($str);
		elseif(strpos($to,'mailto:')===0)
			self::$l->email($str,substr($to,7));
		elseif(strpos($to,'xmpp://')===0)
			self::$l->jabber($str,substr($to,7));
		else
			self::f($str, $to);
	}

	/**
	 * Отправляет очередь сообщений
	 * <br>нужно в случае длительной работы и отправке в jabber
	 */
	public static function sendQuery()
	{
		if (isset(self::$messages['email']))
		{
			foreach (self::$messages['email'] as $k=>$msg)
				error_log(join("\n",$msg), 1, $k);
			unset(self::$messages['email']);
		}
		if (isset(self::$messages['jabber']))
		{
			if (!isset(QFW::$config['jabber']))
				error_log('Jabber не настроен ');
			else
			{
				require_once LIBPATH.'/jabber/XMPPHP/XMPP.php';
				$J = new XMPPHP_XMPP(QFW::$config['jabber']['host'], QFW::$config['jabber']['port'],
					QFW::$config['jabber']['user'], QFW::$config['jabber']['pass'],
					QFW::$config['jabber']['resource'], QFW::$config['jabber']['server'],
					!QFW::$config['QFW']['release'], XMPPHP_Log::LEVEL_ERROR);
				$J->connect();
				$J->processUntil('session_start',10);
				$J->presence();
				foreach (self::$messages['jabber'] as $k=>$msg)
					$J->message($k, join("\n",$msg));
				$J->disconnect();
			}
			unset(self::$messages['jabber']);
		}
	}

	private function jabber($str, $to) { self::$messages['jabber'][$to][]=$str; }
	private function email($str, $to) { self::$messages['email'][$to][]=$str; }
	private static function f($str, $to)
	{
		error_log(date('Y-m-d H:i:s').': '.$str."\n", 3, QFW::$config['host']['logpath'].'/'.$to.'.log');
	}

	public function __destruct()
	{
		self::sendQuery();
	}

}

?>