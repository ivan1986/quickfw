<?php

abstract class Cacher_Abstract
{
    protected static $connection = NULL;
    
    abstract public function set($key, $value);
    abstract public function get($key);
    abstract public function incache($key);
    abstract public function delete($key);
    
}

?>