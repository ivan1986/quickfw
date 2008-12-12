<?php 

	//нужна для того, что сессии используют кешер и они записывают данные
	//после уничтожения всех обьектов, кешер пересоздается заново
	//для записи сессий
	function &getCache($backend='',$opt=array(),$tags=false,$namespace='')
	{
		global $config;
		static $cachers=array();
		if ($backend=='')
			$backend=ucfirst($config['cacher']['module']);
		$key=crc32(serialize(func_get_args()));
		if (isset($cachers[$key]))
			return $cachers[$key];

		$cl='Cacher_'.$backend;
		require_once(QFWPATH.'/Cacher/'.$backend.'.php');
		$c=new $cl;
		if (count($opt)==0 && isset($config['cacher']['options']))
			$opt=$config['cacher']['options'];
		$c->setDirectives($opt);
		if ($namespace!='')
		{
			require_once(QFWPATH.'/QuickFW/Cacher/Namespace.php');
			$c=new Dklab_Cache_Backend_NamespaceWrapper($c,$namespace);
		}
		if ($tags)
		{
			require_once(QFWPATH.'/QuickFW/Cacher/TagEmu.php');
			$c=new Dklab_Cache_Backend_TagEmuWrapper($c);
		}
		$cachers[$key]=&$c;
		return $cachers[$key];
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

?>