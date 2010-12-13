--TEST--
QFW: assigns in view
--FILE--
<?php
require dirname(__FILE__).'/init.php';

QFW::$router->route('assigns');

--EXPECT--
1
array1
array2
array(2) {
  [0]=>
  int(1)
  [1]=>
  int(2)
}
------------------------------------------
1
array1
array2
array(2) {
  [0]=>
  int(1)
  [1]=>
  int(2)
}
local1
------------------------------------------
1
array1
array2
array(2) {
  [0]=>
  int(1)
  [1]=>
  int(2)
}
local2
local3
------------------------------------------
1
array1
array2
array(2) {
  [0]=>
  int(1)
  [1]=>
  int(2)
}
------------------------------------------
