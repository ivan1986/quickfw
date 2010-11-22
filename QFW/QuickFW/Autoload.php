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
				'/controllers/'.
				ucfirst(mb_substr($class, $pos+1));
		}
		else
			$file = QFW::$router->cModule.'/controllers/'.$class;
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
			);
		if (empty(self::$classes[$class]))
			return false;
		require self::$classes[$class];
		return true;
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
