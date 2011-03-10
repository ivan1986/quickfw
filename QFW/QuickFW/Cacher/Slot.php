<?php
/**
 * Dklab_Cache_Frontend_Slot: slot-based caching frontend.
 * 
 * Each cache item has its own "slot" represented by a separate and 
 * typized class in the system. This class knows all about caching
 * specific: dependencies, tags, lifetime etc.
 * 
 * Usage sample:
 * 
 * class Person { ... }
 * class Cache_Slot_PersonName { ... }
 * class Cache_Tag_Person { ... }
 * ...
 * $person = new Person();
 * ...
 * $slot = new Cache_Slot_PersonName($person);
 * if (false === ($data = $slot->load())) {
 *     $name = $person->calculateLongName();
 *     $slot->addTag(new Cache_Tag_Person($person))
 * }
 * ...
 * $tag = new Cache_Tag_Person($person);
 * $tag->clean();
 */
require_once QFWPATH.'/QuickFW/Cacher/Tag.php';
 
abstract class Dklab_Cache_Frontend_Slot
{
    /**
     * Tags attached to this slot.
     * 
     * @var array of Dklab_Cache_Tag
     */
    private $_tags;
    
    /**
     * Calculated ID associated to this slot.
     * 
     * @var string
     */
    private $_id = null;
    
    /**
     * Lifetime of this slot.
     */
    private $_lifetime;

    
    /**
     * Creates a new slot object.
     * 
     * @param string $id   ID of this slot.
     * @return Dklab_Cache_Slot
     */
    public function __construct($id, $lifetime)
    {
        $this->_id = $id;
        $this->_lifetime = $lifetime;
        $this->_tags = array();
    }
    
    
    /**
     * Loads a data of this slot. If nothing is found, returns false.
     * 
     * @return mixed   Complex data or false if no cache entry is found.
     */
    public function load()
    {
        $raw = $this->_getBackend()->load($this->_id);
        return unserialize($raw);
    }
    
    
    /**
     * Saves a data for this slot. 
     * 
     * @param mixed $data   Data to be saved.
     * @return mixed $data
     */
    public function save($data)
    {
        $tags = array();
        foreach ($this->_tags as $tag) {
            $tags[] = $tag->getNativeId();
        }
        $raw = serialize($data);
        $this->_getBackend()->save($raw, $this->_id, $tags, $this->_lifetime);
        return $data;
    }
    
    
    /**
     * Removes a data of specified slot.
     * 
     * @return void
     */
    public function remove()
    {
        $this->_getBackend()->remove($this->_id);
    }
    
    
    /**
     * Associates a tag with current slot.
     * 
     * @param Dklab_Cache_Tag $tag   Tag object to associate.
     * @return void
     */
    public function addTag(Dklab_Cache_Frontend_Tag $tag)
    {
        $this->_tags[] = $tag;
    }

    
    /**
     * Returns Thru-proxy object to call a method with transparent caching.
     * Usage:
     *   $slot = new SomeSlot(...);
     *   $slot->thru($person)->getSomethingHeavy();
     *   // calls $person->getSomethingHeavy() with intermediate caching
     * 
     * @param mixed $obj    Object or classname. May be null if you want to
     *                      thru-call a global function, not a method.
     * @return Dklab_Cache_Frontend_Slot_Thru   Thru-proxy object.
     */
    public function thru($obj)
    {
        return new Dklab_Cache_Frontend_Slot_Thru($this, $obj);
    }

    /**
     * Call a function with transparent caching.
     * Usage:
     *   $slot = new SomeSlot(...);
     *   $data = $slot->get(function() use(...){
     *       ...
     *   });
     *   // calls lamda function() with intermediate caching
     *
     * @param Closure $obj function
     * @return mixed result
     */
    public function get($function)
    {
        $result = $this->load();
        if ($result === false) {
            $result = $function();
            $this->save($result);
        }
        return $result;
    }


    /**
     * Returns backend object responsible for this cache slot.
     * 
     * @return Zend_Cache_Core
     */
    protected abstract function _getBackend();
}


/**
 * Thru-caching helper class.
 */
class Dklab_Cache_Frontend_Slot_Thru
{
    private $_slot;
    private $_obj; 
    
    public function __construct(Dklab_Cache_Frontend_Slot $slot, $obj)
    {
        $this->_slot = $slot;
        $this->_obj = $obj;
    }
    
    public function __call($method, $args)
    {
        if (false === ($result = $this->_slot->load())) {
            if ($this->_obj) {
                $result = call_user_func_array(array($this->_obj, $method), $args);
            } else {
                $result = call_user_func_array($method, $args);
            }
            $this->_slot->save($result);
        }
        return $result;
    }
}
