#!/usr/bin/php
<?

function sig_handler($signo)
{
     switch ($signo) {
         case SIGTERM:
         case SIGINT:
         case SIGHUP:
             // handle shutdown tasks
             exit;
             break;
         default:
             // handle all other signals
     }
}
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");
pcntl_signal(SIGINT,  "sig_handler");


$handle = fopen ("php://stdin","r");

define ('DOC_ROOT', dirname(dirname(__FILE__)).'/www');
define ('ROOTPATH', dirname(dirname(__FILE__)));
define ('APPPATH', ROOTPATH . '/application');
define ('VARPATH', ROOTPATH . '/var');
define ('QFWPATH', ROOTPATH . '/QFW');
define ('LIBPATH', QFWPATH . '/lib');

//Настройки по умолчанию, если нужно - измените
//	define ('TMPPATH', VARPATH . '/tmp');
//	define ('LOGPATH', VARPATH . '/log');
//	define ('COMPATH', APPPATH . '/_common');
//	define ('MODPATH', COMPATH . '/models');

$_SERVER['HTTP_HOST'] = 'shell';

require (QFWPATH.'/Init.php');

QFW::$view->mainTemplate='';

while (true) {
	echo "Welcome to server control shell \n";
	echo "Type exit or quit or q to exit\n";
	echo "Type help for help ;)\n";
	$line = trim(fgets($handle));
	if ($line == 'exit' || $line =='quit' || $line == 'q' || feof($handle))
		break;
	$argv = explode(' ', $line);
	foreach($argv as $k=>$a)
		if (empty($a))
			unset($argv[$k]);
	$argv = join('/',$argv);
	QFW::$router->route($argv, 'Shell');
}
