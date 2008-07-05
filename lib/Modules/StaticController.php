<?php

class StaticController
{
	public function indexAction($dir='',$file='')
	{
		if ($dir=='') {
			QFW::$router->show404();
		}
		if ($file=='') {
			$file='index';
		}
		if (!is_file(QFW::$view->getScriptPath().'/static/'.$dir.'/'.$file.'.tpl')) {
			QFW::$router->show404();
		}
		return QFW::$view->fetch('static/'.$dir.'/'.$file.'.tpl');
	}
}

?>