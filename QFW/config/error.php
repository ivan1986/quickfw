<?php

return false;
/**
 * Настройки обработчика ошибок
 */
$config = array();
$config[] = array(
	'name' => 'mail',
	'RemoveDups' => 300, //секунд или false
	'options' => array(
		'to' => 'ivan1986@localhost',
		'whatToSend' => 65535, // LOG_ALL (look in TextNotifier)
		'subjPrefix' => '[ERROR] ',
		'charset' => 'UTF-8',
	),
);
$config[] = array(
	'name' => 'log',
	'RemoveDups' => 300, //секунд или false
	'options' => array(
		'to' => 'error',
		'whatToSend' => 65535, // LOG_ALL (look in TextNotifier)
		'subjPrefix' => '[ERROR] ',
		'charset' => 'UTF-8',
	),
);

return $config;
