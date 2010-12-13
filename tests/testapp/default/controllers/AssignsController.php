<?php

class AssignsController
{

	public function indexAction()
	{
		QFW::$view->mainTemplate = '';
		QFW::$view->assign('var', 1);
		QFW::$view->assign(array('array1' => 'array1', 'array2' => 'array2'));
		QFW::$view->append('append', 1);
		QFW::$view->append('append', 2);
		echo QFW::$view->fetch('assigns.php');
		echo QFW::$view->fetch('assigns.php', array('local1' => 'local1'));
		echo QFW::$view->fetch('assigns.php', array('local2' => 'local2', 'local3' => 'local3'));
		echo QFW::$view->fetch('assigns.php');
	}

}

