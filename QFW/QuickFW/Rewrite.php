<?php

class QuickFW_Rewrite
{
	private $rewrite = array();
	private $backrewrite = array();
	
	function __construct()
	{
		require APPPATH . '/rewrite.php';
		if (isset($rewrite))
			$this->rewrite = $rewrite;
		if (isset($backrewrite))
			$this->backrewrite = $backrewrite;
	}
	
	function forward($url)
	{
		return preg_replace(array_keys($this->rewrite), array_values($this->rewrite), $url);
	}
	
	function back($url)
	{
		return preg_replace(array_keys($this->backrewrite), array_values($this->backrewrite), $url);
	}
	
}

?>