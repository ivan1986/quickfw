<?php

class ColorController
{
	private $blocks;

	public function __construct()
	{
		$this->blocks=array();
		QFW::$view->P->addJS('colorpicker/colorpicker.js');
		QFW::$view->P->addCSS('colorpicker/colorpicker.css');
	}

	public function inputBlock($name='color',$label='',$value='#000000')
	{
		if (isset($this->blocks[$name]))
			return "ОШИБКА - ПОВТОР БЛОКА";
		$this->blocks[$name]=1;
		return "<span class='select_color' id='SC_".$name."' style='background-color:".$value."'></span>".
		"<label class='select_color' for='SC_".$name."_input'>".$label."</label>".
		"<input id='SC_".$name."_input' name='".$name."' value='".$value."' size='10' maxlength='7' />";
	}

	public function __destruct()
	{
		$b=join('\',\'',array_keys($this->blocks));
		QFW::$view->P->JSe('colorPickerInit(new Array(\''.$b.'\'));');
	}

}

?>