<?php

require_once 'Templater.php';
/**
 * Обертка для Twig
 *
 */
class Templater_Twig extends Templater
{
	/** @var Twig_Environment путь к шаблонам */
	protected $_TE;

	/**
	 * Init - подключение и инициальзация смарти
	 * вынесено так как только по требованию
	 *
	 */
	protected function Init()
	{
		if (!is_file(LIBPATH.'/Twig/Autoloader.php'))
			trigger_error('Install Twig in '.LIBPATH.'/Twig', E_USER_ERROR);
		require LIBPATH.'/Twig/Autoloader.php';
		Twig_Autoloader::register();

		if (null !== $this->_tmplPath)
		{
			$this->_TE = true;
			$this->setScriptPath($this->_tmplPath);
		}
		
		/*$this->_twig->register_resource('block', array(&$this,
													"getTemplate",
													"getTimestamp",
													"isSecure",
													"isTrusted"));
		$this->regPlugs();*/
	}

	/**
	 * Set the path to the templates
	 *
	 * @param string $path The directory to set as the path.
	 * @return void
	 */
	public function setScriptPath($path)
	{
		if (!is_readable($path))
			return false;
		$this->_tmplPath = $path;
		if (!$this->_TE)
			return true;

		$p=explode('/',$path);
		array_pop($p);
		$p=array_pop($p);
		$this->_TE = new Twig_Environment(new Twig_Loader_Filesystem($this->_tmplPath),
			array(
				'cache' => TMPPATH . '/templates_c/'.($p!='templates'?$p:''),
				'debug' => !QFW::$config['QFW']['release'],
				//'auto_reload' => !QFW::$config['QFW']['release'],
			));
		return true;
	}

	public function fetch($name)
	{
		$template = $this->getEngine()->loadTemplate($name);
		$this->assign('P', $this->P);
		return $template->render($this->_vars);
	}

	/**
	 * Return the template engine object
	 *
	 * @return Twig_Environment
	 */
	private function getEngine()
	{
		if (!$this->_TE)
			$this->Init();
		return $this->_TE;
	}

}
?>
