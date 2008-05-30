<?php

class InstallController
{
	
    public function indexAction()
    {
    	global $view;

    	$view->assign('install', true);
    	$view->assign('contentTpl', 'install.tpl');
    	$view->mainTemplate = 'admin/main.tpl';
	}
	
	public function installAction()
	{
		global $db;
		if (!isset($_POST['install']))
		{
			header('Location: /install');
			exit(0);
		}
		
		$dbhost = strtolower(trim($_POST['server']));
		$dbuser = strtolower(trim($_POST['user']));
		$dbpass = $_POST['password'];
		$dbname = strtolower(trim($_POST['database']));
		$dbprefix = strtolower(trim($_POST['prefix']));
		
		$adminname = trim($_POST['adminname']);
		$adminpass = $_POST['adminpass'];
		
		$initemplate =
"; Site configuration data
[host]
host	= $_SERVER[HTTP_HOST]

[database]
type     = mysql
host     = $dbhost
username = $dbuser
password = $dbpass
dbname   = $dbname
prefix   = $dbprefix

[admin]
login	 = $adminname
password = $adminpass";

		$file = ROOTPATH. '/application/config/config.ini';
		file_put_contents($file, $initemplate);
		
		//require_once 'Zend/Config/Ini.php';
		$config = new Zend_Config_Ini(ROOTPATH . '/application/config/config.ini', null);
		
		$db = new QuickFW_AutoDbSimple( $config['database']['username'],
		                                $config['database']['password'],
		                                $config['database']['dbname'],
		                                $config['database']['prefix'],
		                                $config['database']['type'],
		                                $config['database']['host'],
		                                $config['database']['encoding']
		                              );
		
		$queries = file_get_contents(ROOTPATH . '/application/config/photogallery.sql');
		
		$queries = explode(';',$queries);
		
		foreach ($queries as $query)
		{
			if (trim($query) != '')
				$db->query($query);
		}
		rename(__FILE__, __FILE__.'~');
		header('Location: /admin/');
		exit(0);
	}
}
?>