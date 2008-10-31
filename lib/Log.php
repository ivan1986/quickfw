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
		fwrite($this->file, date('r').': '.$str."\n");
	}

	public function __construct($filename)
	{
		$this->file = fopen(TMPPATH.'/'.$filename, 'a+');
	}

	private function __destruct()
	{
		fclose($this->file);
	}
}

?>