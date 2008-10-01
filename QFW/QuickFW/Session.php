<?php

class QuickFW_Session
{

	public $debig_sess;
	public $debig_data;
	public $debig_id;

	static function close()
	{
		return(true);
	}

	static function open()
	{
		return(true);
	}

	static function read($id)
	{
		$data = getCache()->load('sess_'.$id);
		if (!$data)
			return false;
		$_SESSION=unserialize($data);
		return $data;
	}

	static function write($id,$data)
	{
		getCache()->save(serialize($_SESSION),'sess_'.$id);
	}

	static function destroy()
	{
	}

	static function gc()
	{
	}

	public function __construct()
	{
		global $config;
		/*if (!$config['release'])
		{
			//session_save_path(TMPPATH);
			//session_start();
		//echo session_name().':'.session_id().serialize($_SESSION)."\n<br>";
			return;
		}*/
		session_set_save_handler(
				array('QuickFW_Session',"open"),
				array('QuickFW_Session',"close"),
				array('QuickFW_Session',"read"),
				array('QuickFW_Session',"write"),
				array('QuickFW_Session',"destroy"),
				array('QuickFW_Session',"gc")
			);
		session_start();
	}

	public function __destruct()
	{
		//echo session_name().':'.session_id().":".serialize($_SESSION)."\n<br>";
	}

}

?>