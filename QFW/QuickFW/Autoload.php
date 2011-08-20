<?php

/**
 * Класс для автолоада
 *
 * @author ivan1986
 */
class Autoload
{
	/**
	 * Инициализация автолоада
	 * 
	 */
	static public function Init()
	{
		spl_autoload_register(array(__CLASS__, 'Bind'));
		spl_autoload_register(array(__CLASS__, 'Main'));
		spl_autoload_register(array(__CLASS__, 'Cached'));
		//кешированный автолоад
		spl_autoload_register(array(__CLASS__, 'Controller'));
		spl_autoload_register(array(__CLASS__, 'SlotsAndTags'));
		spl_autoload_register(array(__CLASS__, 'Dirs'));
	}

	/**
	 * добавление функции автолоада
	 *
	 * @param string|array $function имя дополнительной функции
	 */
	static public function Add($function = false)
	{
		if (is_array($function))
			foreach($function as $f)
				self::Add($f);
		elseif (is_callable($function))
			spl_autoload_register($function);
	}
	
	/**
	 * Загрузка результатов разбора из кеша
	 *
	 * @param string $class искомый класс
	 */
	static public function Cached($class)
	{
		if (!QuickFW_Cacher_SysSlot::is_use('autoload'))
			return false;
		$C = new QuickFW_Cacher_SysSlot(self::k($class));
		if ($data = $C->load())
		{
			require $data;
			return true;
		}
		return false;
	}

	/**
	 * Автолоад контроллеров (при наследовании)
	 *
	 * @param string $class искомый класс контроллера
	 */
	static public function Controller($class)
	{
		if (mb_strpos($class, 'Controller') === false)
			return false;
		$c = strtr($class,'_','/');
		//пространство имен
		if ($pos = mb_strpos($c, '\\'))
		{
			$ns = strtolower(mb_substr($c, 0, $pos));
			$c = ucfirst(mb_substr($c, $pos+1));
			$Q = $ns.'\QFW';
			//Проверка на саброутинг
			$dir = !class_exists($Q) ? $ns : QFW::$router->cModule.'/'.$ns.'/'.$Q::$router->cModule;
		}
		else
			$dir = QFW::$router->cModule;
		return self::incl($class, APPPATH.'/'.$dir.'/'.QuickFW_Router::CONTROLLERS_DIR.'/'.$c.'.php');
	}

	/**
	 * Классы фреймворка
	 *
	 * <br>Так получилось, что эти классы находятся тут
	 *
	 * @param string $class искомый класс
	 */
	static public function Main($class)
	{
		if (mb_strpos($class, 'QuickFW_') === 0 || mb_strpos($class, 'Templater_') === 0 || mb_strpos($class, 'Cacher_') === 0)
		{
			require QFWPATH.'/'.strtr($class,'_','/').'.php';
			return true;
		}
		return false;
	}

	/**
	 * Автолоад некоторых стандартных классов
	 *
	 * <br>Так получилось, что эти классы находятся тут
	 *
	 * @param string $class искомый класс
	 */
	static public function Bind($class)
	{
		if (empty(self::$classes))
			self::$classes = array(
				'ScaffoldController' => LIBPATH.'/Modules/Scaffold/ScaffoldController.php',
				'Url' => QFWPATH.'/QuickFW/Url.php',
				'Cache' => QFWPATH.'/QuickFW/Cache.php',
				'Hlp' => QFWPATH.'/QuickFW/Helpers.php',
				'Dklab_Cache_Frontend_Slot' => QFWPATH.'/QuickFW/Cacher/Slot.php',
				'Dklab_Cache_Frontend_Tag' => QFWPATH.'/QuickFW/Cacher/Tag.php',
				'QuickFW_Cacher_SysSlot' => QFWPATH.'/QuickFW/Cacher/SysSlot.php',
			);
		if (empty(self::$classes[$class]))
			return false;
		require self::$classes[$class];
		return true;
	}

	/**
	 * Автолоад слотов и тегов
	 *
	 * @param string $class искомый класс
	 */
	static public function SlotsAndTags($class)
	{
		if (strpos($class, 'Slot_') === 0)
		{
			require COMPATH.'/slots/'.substr($class, 5).'.php';
			return true;
		}
		if (strpos($class, 'Tag_') === 0)
		{
			require COMPATH.'/tags/'.substr($class, 4).'.php';
			return true;
		}
		return false;
	}

	/** @var array соответствие классов и файлов */
	static private $classes;

	/**
	 * Автолоад в директориях
	 *
	 * @param string $class искомый класс
	 */
	static public function Dirs($class)
	{
		$list = array(
			LIBPATH,
			MODPATH,
		);
		$c = str_replace('_', '/', $class);
		foreach ($list as $dir)
			if (is_file($dir.'/'.$c.'.php'))
				return self::incl($class, $dir.'/'.$c.'.php');
		return false;
	}
	
	static private function incl($class, $file)
	{
		if (QuickFW_Cacher_SysSlot::is_use('autoload'))
		{
			$C = new QuickFW_Cacher_SysSlot(self::k($class));
			$C->save($file);
		}
		require $file;
		return true;
	}
	
	static private function k($class)
	{
		return 'autoload_'.$class.(QFW::$router ? QFW::$router->cModule : '');
	}

}

?>
