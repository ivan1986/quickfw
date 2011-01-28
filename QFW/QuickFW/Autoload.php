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
	 * @param string|boolean $function имя дополнительной функции
	 */
	static public function Init($function = false)
	{
		spl_autoload_register(array(__CLASS__, 'Bind'));
		spl_autoload_register(array(__CLASS__, 'Dirs'));
		spl_autoload_register(array(__CLASS__, 'Controller'));
		spl_autoload_register(array(__CLASS__, 'SlotsAndTags'));
		if (is_callable($function))
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
			$file =
				strtolower(mb_substr($class, 0, $pos)).
				'/'.QuickFW_Router::CONTROLLERS_DIR.'/'.
				ucfirst(mb_substr($class, $pos+1));
		}
		else
			$file = QFW::$router->cModule.'/'.QuickFW_Router::CONTROLLERS_DIR.'/'.$class;
		require APPPATH.'/'.$file.'.php';
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
				'QuickFW_Auth' => QFWPATH.'/QuickFW/Auth.php',
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
