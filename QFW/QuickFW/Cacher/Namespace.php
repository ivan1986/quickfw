<?php
/**
 * Dklab_Cache - сохраняем начальные копирайты, но код переписан :)
 */
require_once(QFWPATH.'/QuickFW/Cacher/Interface.php');

class Dklab_Cache_Backend_NamespaceWrapper implements Zend_Cache_Backend_Interface 
{
    private $_backend = null;
    private $_namespace = null;
    
    public function __construct(Zend_Cache_Backend_Interface $backend, $namespace)
    {
        $this->_backend = $backend;
        $this->_namespace = $namespace;
    }
    
    public function setDirectives($directives) {return $this->_backend->setDirectives($directives);}
    public function load($id, $doNotTest = false) {return $this->_backend->load($this->_mangleId($id), $doNotTest); }
    public function test($id) {return $this->_backend->test($this->_mangleId($id));}
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $tags = array_map(array($this, '_mangleId'), $tags);
        return $this->_backend->save($data, $this->_mangleId($id), $tags, $specificLifetime);
    }
    public function remove($id) {return $this->_backend->remove($this->_mangleId($id));}
    public function clean($mode = CACHE_CLR_ALL, $tags = array())
    {
        $tags = array_map(array($this, '_mangleId'), $tags);
        return $this->_backend->clean($mode, $tags);
    }
    private function _mangleId($id) {return $this->_namespace . "_" . $id;}
}

?>