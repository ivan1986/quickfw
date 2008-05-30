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
		//$this->debug_data=getCache()->get('sess_'.$id);
		//$this->debug_sess=serialize($_SESSION);
		$_SESSION=unserialize(getCache()->get('sess_'.$id));
		//$this->debug_id=session_id();

		return getCache()->get('sess_'.$id);
	}

	static function write($id,$data)
	{
		//echo "<br>\n";
		//echo session_id().':'.$id.':'.$data.":".serialize($_SESSION)."\n<br>";
		//echo session_id().':'.$this->debug_id.':'.$this->debug_data.":".$this->debug_sess."\n<br>";
		getCache()->set('sess_'.$id,serialize($_SESSION));
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