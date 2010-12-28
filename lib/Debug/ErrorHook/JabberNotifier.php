<?php
/**
 * Sends all notifications to a specified jabber.
 * 
 * Consider using this class together with Debug_ErrorHook_RemoveDupsWrapper
 * to avoid mail server flooding when a lot of errors arrives. 
 */

require_once dirname(__FILE__).'/Util.php';
require_once dirname(__FILE__).'/TextNotifier.php';

class Debug_ErrorHook_JabberNotifier extends Debug_ErrorHook_TextNotifier
{
	private $_to;
	private $_charset;
	private $_whatToSend;
	private $_subjPrefix;
	private static $messages=array();
	
	public function __construct($to, $whatToSend, $subjPrefix = "[ERROR] ", $charset = "UTF-8")
	{
		parent::__construct($whatToSend);
		$this->_to = $to;
		$this->_subjPrefix = $subjPrefix;
		$this->_charset = $charset;
	}
	
	protected function _notifyText($subject, $body)
	{
		$str = $this->_subjPrefix . $subject . "\n\n".$body;
		self::$messages[$this->_to][]=$str;
	}

	/**
	 * Отправляет очередь сообщений
	 * <br>нужно в случае длительной работы и отправке в jabber
	 */
	private static function sendQuery()
	{
		if (count(self::$messages))
		{
			if (!isset(QFW::$config['jabber']))
				error_log('Jabber не настроен ');
			else
			{
				$J = QFW::JabberFromConfig();
				$J->connect();
				$J->processUntil('session_start',10);
				$J->presence();
				foreach (self::$messages as $k=>$msg)
					$J->message($k, join("\n",$msg));
				$J->disconnect();
			}
			self::$messages = array();
		}
	}

	public function __destruct()
	{
		self::sendQuery();
	}

}
