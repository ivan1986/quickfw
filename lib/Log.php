<?php

class Log
{
	private $file;
	protected static $_thisInst = null;

	public static function log($str)
	{
		if (self::$_thisInst === null)
			self::$_thisInst = new Log('general.log');
		self::$_thisInst->l($str);
	}

	public function l($str)
	{
		file_put_contents($this->file, date("d.m.Y H:i:s").': '.$str."\n", FILE_APPEND|LOCK_EX);
	}

	public function __construct($filename)
	{
		$this->file = TMPPATH.'/'.$filename;
	}

}

?>