<?php

class QuickFW_Session
{
	private static $cache;

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
		$data = self::$cache->load('sess_'.$id);
		if (!$data)
			return false;
		$_SESSION=unserialize($data);
		return $data;
	}

	static function write($id,$data)
	{
		if (!empty($_SESSION))
			self::$cache->save(serialize($_SESSION),'sess_'.$id);
		else
			self::$cache->remove('sess_'.$id);
	}

	static function destroy($id)
	{
		self::$cache->remove('sess_'.$id);
		$_SESSION=array();
	}

	static function gc()
	{
		self::$cache->clean(CACHE_CLR_OLD);
	}

	public function __construct()
	{
		self::$cache = Cache::get();
		call_user_func_array('session_set_cookie_params',QFW::$config['session']);
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

}

?>