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
		spl_autoload_register(array(__CLASS__, 'Dirs'));
		if (is_callable($function))
			spl_autoload_register($function);
	}

	/**
	 * Функция автолоада
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
