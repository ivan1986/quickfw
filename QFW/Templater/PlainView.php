<?php

require_once 'Templater.php';

class Templater_PlainView extends Templater
{

	public function fetch($tmpl)
	{
		extract($this->_vars, EXTR_OVERWRITE);
		$P=&$this->P;
		ob_start();
		include($this->_tmplPath . '/' . $tmpl);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

}

?>