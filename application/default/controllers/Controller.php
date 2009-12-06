<?php
/**
 *
 * Description of Controller
 *
 * @author ivan
 */
require (QFWPATH.'/QuickFW/Auth.php');

class Controller extends QuickFW_Auth
{
	public function __construct()
	{
	}

	protected function checkUser()
	{
		return true;
	}

}
?>
