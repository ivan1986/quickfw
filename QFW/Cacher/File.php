<?php

/**
 * Аццки порезанный и переписанный Cache_Lite
 */
class Cacher_File implements Zend_Cache_Backend_Interface
{

	protected $options = array(
		'cacheDir' => '', //directory where to put the cache files (string),
		'caching' => true, //enable / disable caching (boolean),
		'prefix' => 'cache_', //file name prefix (string),
		'lifeTime' => 3600, //cache lifetime in seconds (int),
		'fileLocking' => true, //enable / disable fileLocking (boolean),
		'writeControl' => false, //enable / disable write control (boolean),
		'readControl' => false, //enable / disable read control (boolean),
		'fileNameProtection' => false, //enable / disable automatic file name protection (boolean),
		'automaticCleaningFactor' => 0, //distable / tune automatic cleaning process (int),
		'hashedDirectoryLevel' => 0, //level of the hashed directory system (int),
		'hashedDirectoryUmask' => 0777, //umask for hashed directory structure (int),
	);

/**
 * Constructor
 *
 * @param array $options options
 * @access public
 */

	function __construct($options = array(NULL))
	{
		$this->options['cacheDir']=TMPPATH.'/cache/';
		$this->options=array_merge($this->options, $options);
	}

	public function setDirectives($directives)
	{
		$this->options=array_merge($this->options, $directives);
	}

	public function load($id, $doNotTest = false)
	{
		if (!$this->options['caching'])
			return false;
		if (is_array($id))
		{
			$x = array();
			foreach($id as $v)
				$x[$v] = $this->load($v);
			return $x;
		}
		$file = $this->fileName($id);
		if (!is_file($file))
			return false;
		$data = false;
		if ($doNotTest || filemtime($file) > time())
			$data = $this->_read($file);
		$data = unserialize($data);
		return $data;
	}

	public function save($data, $id, $tags = array(), $specificLifetime = false)
	{
		if (!$this->options['caching'])
			return false;
		$data = serialize($data);
		$file = $this->fileName($id,true);

		if ($this->options['automaticCleaningFactor']>0 && rand(1, $this->options['automaticCleaningFactor']) == 1)
			$this->cleanDir($this->options['cacheDir'], CACHE_CLR_OLD);

		$control = $this->options['readControl'] ? $this->hash($data) : '';
		if (file_put_contents($file, $control.$data, ($this->options['fileLocking'] ? LOCK_EX : NULL)) === false)
			return false;
		$time = $specificLifetime!==false ? $specificLifetime : $this->options['lifeTime'];
		if ($time === null)
			$time = 307584000; //лет на 10 хватит
		touch($file, time() + $time);
		if (!$this->options['writeControl'] || $data == $this->_read($file))
			return true;
		$this->unlink($file);
		return false;
	}

	public function remove($id)
	{
		return $this->unlink($this->fileName($id));
	}

	public function clean($mode = CACHE_CLR_ALL, $tags = array())
	{
		return $this->cleanDir($this->options['cacheDir'], $mode);
	}

	public function test($id)
	{
		$file = $this->fileName($id);
		return is_file($file) && filemtime($file) > time();
	}

	protected function unlink($file)
	{
		return is_file($file) && unlink($file);
	}

	protected function cleanDir($dir,$mode=CACHE_CLR_ALL)
	{
		if (!($dh = opendir($dir)))
			return false;

		$result = true;
		while ($file = readdir($dh))
		{
			if (($file == '.') || ($file == '..') || (substr($file, 0, 6)!=$this->options['prefix']))
				continue;
			$file2 = $dir . $file;
			if (strpos($file2, $this->options['prefix']) === false)
				continue;

			if (is_dir($file2) && $this->options['hashedDirectoryLevel']>0)
				$result = $result && $this->cleanDir($file2 . '/', $mode);

			if (!is_file($file2))
				continue;
			if ($mode == CACHE_CLR_OLD && filemtime($file2) > time())
				continue;
			$result = $result && $this->unlink($file2);
		}
		return $result;
	}

	/**
	 * Функция возвращает имя файла по ключу кеша
	 * В случае разбития по каталогам и установки флага создает каталог
	 * Создание каталога используется при записи файла
	 * Объеденино было из-за оптимизации и удаления дублирующего кода
	 */
	protected function fileName($id,$createDirs=false)
	{
		//слеш у нас являются разделителем
		//А если всякие операционные системы не понимают двоеточия, две точки подрят
		//кавычки и прочие извращения, то это личная сексуальная драмма их пользователей
		$fname = $this->options['fileNameProtection'] ? md5($id) : strtr($id, '/', '-');
		$suffix = $this->options['prefix'].$fname;
		$root = $this->options['cacheDir'];
		if ($this->options['hashedDirectoryLevel']>0)
		{
			$hash = md5($suffix);
			for ($i=0 ; $i<$this->options['hashedDirectoryLevel'] ; $i++)
				$root .= $this->options['prefix'] . substr($hash, 0, $i + 1) . '/';
			if ($createDirs && !is_dir($root))
				mkdir($root, $this->options['hashedDirectoryUmask'] , true);
		}
		return $root.$suffix;
	}

	protected function _read($file)
	{
		$data = file_get_contents($file);
		if ($data === false)
			return false;
		if (!$this->options['readControl'])
			return $data;
		$hashControl = substr($data, 0, 32);
		$data = substr($data, 32);
		$hashData = $this->hash($data);
		if ($hashData == $hashControl)
			return $data;
		$this->unlink($file);
		return false;
	}

	protected function hash($data)
	{
		return sprintf('% 32d', crc32($data));
	}
}

?>
