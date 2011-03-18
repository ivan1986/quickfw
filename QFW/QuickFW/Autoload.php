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
		spl_autoload_register(array(__CLASS__, 'Dirs'));
		spl_autoload_register(array(__CLASS__, 'Controller'));
		spl_autoload_register(array(__CLASS__, 'SlotsAndTags'));
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
	 * Автолоад контроллеров (при наследовании)
	 *
	 * @param string $class искомый класс контроллера
	 */
	static public function Controller($class)
	{
		if (mb_strpos($class, 'Controller') === false)
			return false;
		$class = strtr($class,'_','/');
		//пространство имен
		if ($pos = mb_strpos($class, '\\'))
		{
			$ns = strtolower(mb_substr($class, 0, $pos));
			$class = ucfirst(mb_substr($class, $pos+1));
			$Q = $ns.'\QFW';
			//Проверка на саброутинг
			$dir = !class_exists($Q) ? $ns : QFW::$router->cModule.'/'.$ns.'/'.$Q::$router->cModule;
		}
		else
			$dir = QFW::$router->cModule;
		$file = $dir.'/'.QuickFW_Router::CONTROLLERS_DIR.'/'.$class;
		require APPPATH.'/'.$file.'.php';
		return true;
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
		if (mb_strpos($class, 'QuickFW') === false)
			return false;
		$class = strtr($class,'_','/');
		require QFWPATH.'/'.$class.'.php';
		return true;
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
			require APPPATH.'/_common/slots/'.substr($class, 5).'.php';
			return true;
		}
		if (strpos($class, 'Tag_') === 0)
		{
			require APPPATH.'/_common/tags/'.substr($class, 4).'.php';
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
		$class = str_replace('_', '/', $class);
		foreach ($list as $dir)
			if (is_file($dir.'/'.$class.'.php'))
			{
				require $dir.'/'.$class.'.php';
				return true;
			}
		return false;
	}

}

?>
