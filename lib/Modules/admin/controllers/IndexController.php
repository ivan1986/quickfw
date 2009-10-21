<?php

require ('Controller.php');

class IndexController extends Controller
{

	public function indexAction()
	{
		return 'Админка';
	}

	public function logoutAction()
	{
		unset($_SESSION['admin']);
		QFW::$router->redirectMCA('admin');
	}

}

?>
