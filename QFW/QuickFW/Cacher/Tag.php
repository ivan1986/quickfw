<?php
/**
 * Dklab_Cache_Frontend_Tag: slot-based tag implementation.
 * 
 * You may create a cache slot and add a bunch of tugs to it.
 * Tags are typized; each tag is parametrized according to 
 * specific needs.
 */
 
abstract class Dklab_Cache_Frontend_Tag
{
    /**
     * Calculated ID associated to this slot.
     * 
     * @var string
     */
    private $_id = null;


    /**
     * Creates a new Tag object.
     *
     * @return Dklab_Cache_Tag
     */
    public function __construct($id)
    {
        $this->_id = $id;
    }
    
    
    /**
     * Clears all keys associated to this tags.
     * 
     * @return void
     */
    public function clean()
    {
        $this->getBackend()->clean(CACHE_CLR_TAG, array($this->getNativeId()));
    }
    

    /**
     * Returns backend object responsible for this cache tag.
     * This method has to be public, because we use it in Slot::addTag()
     * to check equality of tag and slot backends.
     * 
     * @return Zend_Cache_Backend_Interface
     */
    public abstract function getBackend();


    /**
     * Returns generated ID of this tag.
     * This method must be public, because it is used in Slot.
     * 
     * @return string    Tag name.
     */
    public function getNativeId()
    {
        return $this->_id;
    }
}
