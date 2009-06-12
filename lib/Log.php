<?php

class Log
{
	private static $l=null;
	private static $messages=array();

	/**
	 * Диспетчеризация сообщения - отправляет в нужный логер или в файл
	 */
	public static function log($str,$to='general')
	{
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

	public function __construct() {}

	private function jabber($str, $to) { self::$messages['jabber'][$to][]=$str; }
	private function email($str, $to) { self::$messages['email'][$to][]=$str; }
	private static function f($str, $to)
	{
		error_log(date('Y-m-d H:i:s').': '.$str."\n", 3, QFW::$config['host']['logpath'].'/'.$to.'.log');
	}

	public function __destruct()
	{
		if (isset(self::$messages['email']))
			foreach (self::$messages['email'] as $k=>$msg)
				error_log(join("\n",$msg), 1, $k);
		if (isset(self::$messages['jabber']))
		{
			if (!isset(QFW::$config['jabber']))
				error_log('Jabber не настроен '.join("\n",$msg));
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
		}
	}

}

?>