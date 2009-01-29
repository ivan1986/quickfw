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
		if (array_key_exists('cookie_domain',QFW::$config))
			ini_set('session.cookie_domain',QFW::$config['cookie_domain']);
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