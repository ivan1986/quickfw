<?php

class QuickFW_Params
{
	private $Params = array();
	
	public $curName='';
	
	public function gCurModuleParams()
	{
		return isset($this->Params[$this->curName])?$this->Params[$this->curName]:null;
	}
	
	public function sModuleParams($name,$params)
	{
		$this->Params[$name]=$params;
	}
}

?>