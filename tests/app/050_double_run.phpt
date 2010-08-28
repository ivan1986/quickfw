--TEST--
QFW: double run route
--FILE--
<?php
require dirname(__FILE__).'/init.php';

QFW::$router->route('');
echo "\n";
QFW::$router->route('');

--EXPECT--
Основной шаблон
Корневое действие сайта
Основной шаблон
Корневое действие сайта
