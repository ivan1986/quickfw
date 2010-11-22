--TEST--
QFW: test _SERVER HTTP_HOST
--FILE--
<?php

define ('DOC_ROOT', dirname(__FILE__));
define ('ROOTPATH', dirname(dirname(dirname(__FILE__))));
define ('APPPATH', ROOTPATH . '/application');
define ('VARPATH', ROOTPATH . '/var');
define ('QFWPATH', ROOTPATH . '/QFW');
define ('LIBPATH', ROOTPATH . '/lib');

require (QFWPATH.'/Init.php');

QFW::$router->route();

--EXPECT--
$_SERVER['HTTP_HOST'] NOT SET
