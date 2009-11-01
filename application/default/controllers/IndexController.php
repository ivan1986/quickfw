<?php

require_once(QFWPATH.'/QuickFW/Auth.php');

class IndexController extends QuickFW_Auth
{
	public function __construct()
	{
		echo QFW::$router->module.'.'.QFW::$router->controller.'.'.QFW::$router->action."<br>\n";
	}

	public function indexAction()
	{
		print_r($_POST);
		require_once LIBPATH.'/MetaForm/MetaFormAction.php';
		require_once LIBPATH.'/MetaForm/MetaForm.php';
		require_once LIBPATH.'/MetaForm/FormPersister.php';
		$SemiParser = new HTML_SemiParser();
		ob_start(array(&$SemiParser, 'process'));

		$MetaForm = new HTML_MetaForm('secret_secret');
		$SemiParser->addObject($MetaForm);

		$FormPersister = new HTML_FormPersister();
		$SemiParser->addObject($FormPersister);

		$metaFormAction = new HTML_MetaFormAction($MetaForm);
		print_r($metaFormAction->process());
		print_r($metaFormAction->getErrors());
		print_r($MetaForm->getFormMeta());

		//$x = Cache::slot('Test',1);
		return QFW::$view->fetch('b.html');

		return 'Корневое действие сайта, показывается на /, на /default, на /index и т.п.<br/>';
	}

	public function testBlock()
	{
		echo 2;
	}

	public function преведAction()
	{
		QFW::$view->mainTemplate='';
		return "медвед";
	}

}

?>