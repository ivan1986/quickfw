<?php

require_once 'AdminAbstract.php';

class IndexController extends Admin_Controller_Abstract 
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function indexAction()
	{
		echo 'Главная страница админки. Защищенная зона';
	}
}

?>