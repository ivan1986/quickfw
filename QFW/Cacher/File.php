<?php
/**
 * Cache_Lite с переписанным интерфейсом
 */

require_once(QFWPATH.'/QuickFW/Cacher/Interface.php');

class Cacher_File implements Zend_Cache_Backend_Interface 
{
	
	protected $_cacheDir;
	protected $_caching = true;
	protected $_lifeTime = 3600;//null;
	protected $_fileLocking = true;
	protected $_refreshTime;
	protected $_file;
	protected $_fileName;
	protected $_writeControl = false;
	protected $_readControl = false;
	protected $_fileNameProtection = true;
	protected $_automaticSerialization = true;
	protected $_automaticCleaningFactor = 0;
	protected $_hashedDirectoryLevel = 0;
	protected $_hashedDirectoryUmask = 0777;

/**
* Constructor
*
* $options is an assoc. Available options are :
* $options = array(
*     'cacheDir' => directory where to put the cache files (string),
*     'caching' => enable / disable caching (boolean),
*     'lifeTime' => cache lifetime in seconds (int),
*     'fileLocking' => enable / disable fileLocking (boolean),
*     'writeControl' => enable / disable write control (boolean),
*     'readControl' => enable / disable read control (boolean),
*     'fileNameProtection' => enable / disable automatic file name protection (boolean),
*     'automaticSerialization' => enable / disable automatic serialization (boolean),
*     'automaticCleaningFactor' => distable / tune automatic cleaning process (int),
*     'hashedDirectoryLevel' => level of the hashed directory system (int),
*     'hashedDirectoryUmask' => umask for hashed directory structure (int),
*     'errorHandlingAPIBreak' => API break for better error handling ? (boolean)
* );
*
* @param array $options options
* @access public
*/

	function Cacher_File($options = array(NULL)){
		$this->_cacheDir=TMPPATH.'/cache/';
		foreach($options as $key => $value) {
			$this->setOption($key, $value);
		}
	}

    public function setDirectives($directives)
    {
		foreach($directives as $key => $value) {
			$this->setOption($key, $value);
		}
    }
	
	function setOption($name, $value) {
    $availableOptions = ';hashedDirectoryUmask;hashedDirectoryLevel;automaticCleaningFactor;automaticSerialization;fileNameProtection;cacheDir;caching;lifeTime;fileLocking;writeControl;readControl;';
		if (strpos($availableOptions, ';'.$name.';') !== false) {
			$property = '_'.$name;
			$this->$property = $value;
		}
	}

	public function load($id, $doNotTest = false){
		if (!$this->_caching) return false;
		$data = false;
		$this->_setRefreshTime();
		$this->_setFileName($id);
		if (($doNotTest) || (is_null($this->_refreshTime))) {
			if (file_exists($this->_file)) {
				$data = $this->_read();
			}
		} else {
			if ((file_exists($this->_file)) && (filemtime($this->_file) > $this->_refreshTime)) {
				$data = $this->_read();
			}
		}
		if (($this->_automaticSerialization) and (is_string($data))) {
			$data = unserialize($data);
		}
		return $data;
	}

	public function save($data, $id, $tags = array(), $specificLifetime = false){
		if (!$this->_caching) return false;
		if ($this->_automaticSerialization) {
			$data = serialize($data);
		}
		$this->_setFileName($id);
		if ($this->_automaticCleaningFactor>0) {
			$rand = rand(1, $this->_automaticCleaningFactor);
			if ($rand==1) {
				$this->_cleanDir($this->_cacheDir, 'old');
			}
		}
		if ($this->_writeControl) {
			if (!$this->_writeAndControl($data)) {
				$this->_unlink($this->_file);
				return false;
			} else {
				return true;
			}
		}
		return $this->_write($data);
	}

    public function remove($id){
		$this->_setFileName($id);
		return $this->_unlink($this->_file);
	}

    public function clean($mode = CACHE_CLR_ALL, $tags = array()){
		return $this->_cleanDir($this->_cacheDir);
	}

	public function setLifeTime($newLifeTime){
		$this->_lifeTime = $newLifeTime;
		$this->_setRefreshTime();
	}

    public function test($id){
		$this->_setFileName($id);
		return @filemtime($this->_file);
	}

	function extendLife() {
	  @touch($this->_file);
	}


	protected function _setRefreshTime() {
		$this->_refreshTime = ($this->_lifeTime === null) ? null : time() - $this->_lifeTime;
	}

	protected function _unlink($file){
		return is_file($file) && unlink($file);
	}

	protected function _cleanDir($dir,$mode='ingroup'){
		$motif = 'cache_';
		if (!($dh = opendir($dir))) {
			return false;
		}
		$result = true;
		while ($file = readdir($dh)) {
			if (($file != '.') && ($file != '..') && (substr($file, 0, 6)=='cache_')) {
 				$file2 = $dir . $file;
 				if (is_file($file2)) {
 					switch (substr($mode, 0, 9)) {
 						case 'old':
 							if (!is_null($this->_lifeTime) && (mktime() - filemtime($file2) > $this->_lifeTime)) {
								$result = ($result && $this->_unlink($file2));
 							}
 							break;
 						case 'ingroup':
 						default:
 							if (strpos($file2, $motif) !== false) {
 								$result = ($result && $this->_unlink($file2));
 							}
 						break;
 					}
 				} elseif (is_dir($file2) && ($this->_hashedDirectoryLevel>0)) {
 					$result = ($result && ($this->_cleanDir($file2 . '/', $group, $mode)));
 				}
			}
		}
		return $result;
	}

	protected function _setFileName($id){
		if ($this->_fileNameProtection) {
			$suffix = 'cache_'.md5($id);
		} else {
			$suffix = 'cache_'.$id;
		}
		$root = $this->_cacheDir;
		if ($this->_hashedDirectoryLevel>0) {
			$hash = md5($suffix);
			for ($i=0 ; $i<$this->_hashedDirectoryLevel ; $i++) {
				$root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
			}   
		}
		$this->_fileName = $suffix;
		$this->_file = $root.$suffix;
	}

	protected function _read(){
		$mqr = get_magic_quotes_runtime();
		set_magic_quotes_runtime(0);
		$data = file_get_contents($this->_file);
		set_magic_quotes_runtime($mqr);
		if ($data === false){
			return false;
		}
		if ($this->_readControl) {
			$hashControl = substr($data, 0, 32);
			$data = substr($data, 32);
			$hashData = $this->_hash($data);
			if ($hashData != $hashControl) {
				$this->_unlink($this->_file);
				return false;
			}
		}
		return $data;
	}

	protected function _write($data){
	    if ($this->_hashedDirectoryLevel > 0) {
	      $hash = md5($this->_fileName);
	      $root = $this->_cacheDir;
	      for ($i=0 ; $i<$this->_hashedDirectoryLevel ; $i++) {
	        $root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
	        is_dir($root) || mkdir($root, $this->_hashedDirectoryUmask);
	      }
	    }
		$control = $this->_readControl ? $this->_hash($data) : '';
		return file_put_contents($this->_file, $control.$data, ($this->_fileLocking ? LOCK_EX : NULL)) !== false;
	}

	protected function _writeAndControl($data){
		$this->_write($data);
		$dataRead = $this->_read($data);
		return ($dataRead==$data);
	}

	protected function _hash(&$data){
		return sprintf('% 32d', crc32($data));
	}
} 


?>