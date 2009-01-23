<?php

class Log
{

	public static function log($str,$file='general')
	{
		file_put_contents(QFW::$config['host']['logpath'].'/'.$file.'.log', date('Y-m-d H:i:s').': '.$str."\n", FILE_APPEND | LOCK_EX);
	}

	public function __construct() {}

}

?>