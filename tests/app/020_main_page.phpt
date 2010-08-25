--TEST--
QFW: main page test - default MCA
--FILE--
<?php
require dirname(__FILE__).'/init.php';

QFW::$router->route('');

--EXPECT--
Основной шаблон
Корневое действие сайта
