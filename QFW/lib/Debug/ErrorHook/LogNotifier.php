<?php
/**
 * Sends all notifications to a specified jabber.
 * 
 * Consider using this class together with Debug_ErrorHook_RemoveDupsWrapper
 * to avoid mail server flooding when a lot of errors arrives. 
 */

require_once dirname(__FILE__).'/Util.php';
require_once dirname(__FILE__).'/TextNotifier.php';
require_once LIBPATH.'/Log.php';

class Debug_ErrorHook_LogNotifier extends Debug_ErrorHook_TextNotifier
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
		$str = $this->_subjPrefix . $subject . "\n".$body;
		Log::out($str, $this->_to);
	}

}
