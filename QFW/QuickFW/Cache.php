<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

define('CACHE_CLR_ALL','all');
define('CACHE_CLR_OLD','old');
define('CACHE_CLR_TAG','tag');
define('CACHE_CLR_NOT_TAG','notTag');

/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Cache_Backend_Interface
{
	/**
	 * Set the frontend directives
	 *
	 * @param array $directives assoc of directives
	 */
	public function setDirectives($directives);

	/**
	 * Test if a cache is available for the given id and (if yes) return it (false else)
	 *
	 * Note : return value is always "string" (unserialization is done by the core not by the backend)
	 *
	 * @param  string|array  $id                     Cache id
	 * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
	 * @return string|false cached datas
	 */
	public function load($id, $doNotTest = false);

	/**
	 * Test if a cache is available or not (for the given id)
	 *
	 * @param  string|array $id cache id
	 * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
	 */
	public function test($id);

	/**
	 * Save some string datas into a cache record
	 *
	 * Note : $data is always "string" (serialization is done by the
	 * core not by the backend)
	 *
	 * @param  string $data            Datas to cache
	 * @param  string $id              Cache id
	 * @param  array $tags             Array of strings, the cache record will be tagged by each string entry
	 * @param  int   $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
	 * @return boolean true if no problem
	 */
	public function save($data, $id, $tags = array(), $specificLifetime = false);

	/**
	 * Remove a cache record
	 *
	 * @param  string $id Cache id
	 * @return boolean True if no problem
	 */
	public function remove($id);

	/**
	 * Clean some cache records
	 *
	 * Available modes are :
	 * CACHE_CLR_ALL (default)    => remove all cache entries ($tags is not used)
	 * CACHE_CLR_OLD              => remove too old cache entries ($tags is not used)
	 * CACHE_CLR_TAG     => remove cache entries matching all given tags
	 *                                               ($tags can be an array of strings or a single string)
	 * CACHE_CLR_NOT_TAG => remove cache entries not {matching one of the given tags}
	 *                                               ($tags can be an array of strings or a single string)
	 *
	 * @param  string $mode Clean mode
	 * @param  array  $tags Array of tags
	 * @return boolean true if no problem
	 */
	public function clean($mode = CACHE_CLR_ALL, $tags = array());

}

/**
 * Класс со статическими функциями
 * фабрика объектов для работы с кешем
 */
class Cache
{
	static $cachers=array();

	private function __construct() {}

	/**
	 * Фабрика кешеров
	 *
	 * @param string $name кешер - из массива или стандартный
	 * @param string $namespace пространство имен
	 * @return Zend_Cache_Backend_Interface кешер
	 */
	public static function get($name='default', $ns='')
	{
		if (isset(self::$cachers[$name.'_'.$ns]))
			return self::$cachers[$name.'_'.$ns];
		if (isset(self::$cachers['__'.$name]))
			$c = self::$cachers['__'.$name];
		else
		{
			if (isset(QFW::$config['cache'][$name]))
			{
				$data = QFW::$config['cache'][$name];
				$backend = ucfirst($data['module']);
			}
			else
				$backend = ucfirst($name);
			$cl='Cacher_'.$backend;
			require_once QFWPATH.'/Cacher/'.$backend.'.php';
			$c=new $cl;
			$c->setDirectives(
				(isset($data['options']) && is_array($data['options']))
				? $data['options'] : array()
			);
			self::$cachers['__'.$name] = $c;
		}

		// если у нас не пустое пространство имен - юзаем проксирующий класс
		$n = (isset($data['namespace']) ? $data['namespace'] : '').$ns;
		if ($n)
			$c=self::ns($c,$n);
		if (!empty($data['tags']))
		{
			require_once(QFWPATH.'/QuickFW/Cacher/TagEmu.php');
			$c=new Dklab_Cache_Backend_TagEmuWrapper($c);
		}
		return self::$cachers[$name.'_'.$ns]=$c;
	}

	/**
	 * Фабрика пространств имен
	 *
	 * @param string $name имя пространства имен
	 * @return Dklab_Cache_Backend_NamespaceWrapper новое пространство имен
	 */
	public static function ns(Zend_Cache_Backend_Interface $cacher, $ns='')
	{
		require_once(QFWPATH.'/QuickFW/Cacher/Namespace.php');
		return new Dklab_Cache_Backend_NamespaceWrapper($cacher,$ns);
	}

	/**
	 * Фабрика слотов
	 *
	 * @deprecated Используйте autoload
	 * @param string $name имя слота
	 * @return Dklab_Cache_Frontend_Slot новый слот
	 */
	public static function slot($name)
	{
		trigger_error('Используйте autoload', E_USER_NOTICE);
		require_once QFWPATH.'/QuickFW/Cacher/Slot.php';
		require_once COMPATH.'/slots/'.$name.'.php';
		$args = func_get_args();
		array_shift($args);
		$reflectionObj = new ReflectionClass('Slot_'.$name);
		return $reflectionObj->newInstanceArgs($args);
	}

	/**
	 * Фабрика тегов
	 *
	 * @deprecated Используйте autoload
	 * @param string $name имя тега
	 * @return Dklab_Cache_Frontend_Tag новый тег
	 */
	public static function tag($name)
	{
		trigger_error('Используйте autoload', E_USER_NOTICE);
		require_once QFWPATH.'/QuickFW/Cacher/Tag.php';
		require_once COMPATH.'/tags/'.$name.'.php';
		$args = func_get_args();
		array_shift($args);
		$reflectionObj = new ReflectionClass('Tag_'.$name);
		return $reflectionObj->newInstanceArgs($args);
	}

}

class Cache_Thru
{
	private $_cacher, $_obj, $_id, $_tags, $_lt;

	public function __construct($Cacher, $obj, $id, $tags, $lifeTime)
	{
		$this->_cacher = $Cacher;
		$this->_obj = $obj;
		$this->_id = $id;
		$this->_tags = $tags;
		$this->_lt = $lifeTime;
	}

	public function __call($method, $args)
	{
		if (false === ($result = $this->_cacher->load($this->_id))) {
			$result = call_user_func_array($this->_obj?array($this->_obj, $method):$method, $args);
			$this->_cacher->save($result, $this->_id, $this->_tags, $this->_lt);
		}
		return $result;
	}
}

function thru($Cacher, $obj, $id, $tags=array(), $lifeTime=null)
{
	return new Cache_Thru($Cacher, $obj, $id, $tags, $lifeTime);
}
