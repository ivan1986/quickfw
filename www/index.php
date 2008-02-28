<?php
	//Для отладки без сервера
	if (!isset($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = getcwd();
	define ('DOC_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));
	define ('ROOTPATH', realpath($_SERVER['DOCUMENT_ROOT'] . '/../'));
	define ('LIBPATH', ROOTPATH . '/library');
	
	require (LIBPATH.'/Init.php');

	require (LIBPATH.'/QuickFW/Router.php');
	$router = new QuickFW_Router(ROOTPATH . '/application');
	$router->route();
?>