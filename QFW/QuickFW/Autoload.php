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
		if (is_callable($function))
			spl_autoload_register($function);
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
				'ScafoldController' => LIBPATH.'/Modules/Scafold/ScafoldController.php',
				'QuickFW_Auth' => QFWPATH.'/QuickFW/Auth.php',
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
