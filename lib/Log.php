<?php

class Log
{
	private $file;
	protected static $_thisInst = array();

	public static function log($str,$file='general')
	{
		//file_put_contents(LOGPATH.'/'.$file.'.log', date('Y-m-d H:i:s').': '.$str."\n", FILE_APPEND | LOCK_EX);  ???
		if (!array_key_exists($file,self::$_thisInst))
			self::$_thisInst[$file] = new Log($file.'.log');
		self::$_thisInst[$file]->l($str);
	}

	public function l($str)
	{
		file_put_contents($this->file, date('Y-m-d H:i:s').': '.$str."\n", FILE_APPEND | LOCK_EX);
	}

	public function __construct($filename)
	{
		$this->file = LOGPATH.'/'.$filename;
	}

}

?>