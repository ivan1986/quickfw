<?php

/**
 * Обертка для Twig
 *
 */
class Templater_Twig
{
	/** @var Twig_Environment путь к шаблонам */
	protected $_TE;

	/** @var QuickFW_Plugs Плагины фреймворка */
	public $P;

	protected $_vars;
	protected $_tmplPath;
	
	/** @var string Template file in which is all content */
	public $mainTemplate;

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
	 * Constructor
	 *
	 * @param string $tmplPath
	 * @param string $mainTpl
	 * @return void
	 */
	public function __construct($tmplPath, $mainTpl)
	{
		$this->_vars = array();
		$this->_tmplPath = $tmplPath;
		$this->mainTemplate = $mainTpl;
		$this->P = QuickFW_Plugs::getInstance();
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
			array( 'cache' => TMPPATH . '/templates_c/'.($p!='templates'?$p:'') ));
		return true;
	}

	/**
	 * Retrieve the current template directory
	 *
	 * @return string
	 */
	public function getScriptPath()
	{
		return $this->_tmplPath;
	}

	/**
	 * Assign variables to the template
	 *
	 * Allows setting a specific key to the specified value, OR passing an array
	 * of key => value pairs to set en masse.
	 *
	 * @see __set()
	 * @param string|array $spec The assignment strategy to use (key or array of key
	 * => value pairs)
	 * @param mixed $value (Optional) If assigning a named variable, use this
	 * as the value.
	 * @return void
	 */
	public function assign($spec, $value = null)
	{
		if (is_array($spec))
			$this->_vars = array_merge($this->_vars, $spec);
		else
			$this->_vars[$spec] = $value;
		return $this;
	}

	/**
	 * Clear assigned variable
	 *
	 * @param string|array
	 * @return void
	 */
	public function delete($key)
	{
		if (is_array($spec))
			foreach ($spec as $item)
				unset($this->_vars[$item]);
		else
			unset($this->_vars[$spec]);
	}

	/**
	 * Clear all assigned variables
	 *
	 * @return void
	 */
	public function clearVars()
	{
		$this->_vars=array();
	}

	public function getTemplateVars($var = null)
	{
		if ($var === null)
			return $this->_vars;
		elseif (isset($this->_vars[$var]))
			return $this->_vars[$var];
		else
			return null;
	}

	public function block($block)
	{
		//TODO: убрать ненужную переменную после перехода на php 5.3
		$args = func_get_args();
		return call_user_func_array(array(&QFW::$router, 'blockRoute'), $args);
	}

	/**
	 * Processes a template and returns the output.
	 *
	 * @param string $name The template to process.
	 * @return string The output.
	 */
	public function render($name)
	{
		return $this->fetch($name);
	}

	public function fetch($name)
	{
		$template = $this->getEngine()->loadTemplate($name);
		return $template->render($this->_vars);
	}

	public function displayMain($content)
	{
		if (isset($this->mainTemplate) && $this->mainTemplate!="")
		{
			//Необходимо для установки флага CSS
			$this->P->startDisplayMain();
			$this->assign('content',$content);
			$content = $this->fetch($this->mainTemplate);
		}
		//Необходимо для вызовов всех деструкторов
		QFW::$router->startDisplayMain();
		return $this->P->HeaderFilter($content);
	}

	/**
	 * Return the template engine object
	 *
	 * @return Smarty
	 */
	private function getEngine()
	{
		if (!$this->_TE)
			$this->Init();
		return $this->_TE;
	}

	/**
	 * @internal Функция для Smarty
	 */
	public static function getTemplate($tpl_name, &$tpl_source, &$smarty)
	{
		$tpl_source = '{literal}'.QFW::$router->blockRoute($block).'{/literal}';
		return true;
	}

	/**
	 * @internal Функция для Smarty
	 */
	public function getTimestamp($tpl_name, &$tpl_timestamp, &$smarty)
	{
		$tpl_timestamp = microtime(true);
		return true;
	}

	/**
	 * @internal Функция для Smarty
	 */
	public function isSecure($tpl_name, &$smarty)
	{
		return true;
	}

	/**
	 * @internal Функция для Smarty
	 */
	public function isTrusted($tpl_name, &$smarty)
	{
		return false;
	}

	/**
	 * Функция регистрирует плагины в Smarty
	 */
	protected function regPlugs()
	{
		$this->_twig->register_function('baseUrl',array($this,'s_baseUrl'));
		$this->_twig->register_function('siteUrl',array($this,'s_siteUrl'));
		$this->_twig->register_modifier('siteUrl',array($this,'m_siteUrl'));

		$this->_twig->register_function('addJS',array($this,'s_addJS'));
		$this->_twig->register_function('addCSS',array($this,'s_addCSS'));

		$this->_twig->register_function('outHead',array($this,'s_outHead'));
		$this->_twig->register_block('getHead',array($this,'s_getHead'));
		$this->_twig->register_function('oJS',array($this,'s_oJS'));
		$this->_twig->register_block('gJS',array($this,'s_gJS'));
		$this->_twig->register_block('gJSh',array($this,'s_gJSh'));
		$this->_twig->register_block('gJSe',array($this,'s_gJSe'));
		$this->_twig->register_block('gCSS',array($this,'s_gCSS'));

		$this->_twig->register_function('pluralForm',array($this,'s_pluralForm'));
	}

	public function s_pluralForm($params, &$smarty)
	{
		$sep=isset($params['sep'])?$params['sep']:',';
		$forms=explode($sep,$params['forms'],3);
		return $this->P->pluralForm($params['num'],$forms[0],$forms[1],$forms[2]);
	}

	public function s_baseUrl($params, &$smarty)
		{return $this->P->baseUrl();}
	public function s_siteUrl($params, &$smarty)
		{return !isset($params['url'])?$this->P->baseUrl():$this->P->siteUrl($params['url']);}
	public function m_siteUrl($url)
		{return $this->P->siteUrl($url);}
	public function s_addJS($params, &$smarty)
	{
		if (isset($params['file']))
			$this->P->addJS($params['file'],isset($params['noBase']));
		return "";
	}
	public function s_addCSS($params, &$smarty)
	{
		if (isset($params['file']))
			$this->P->addCSS($params['file'],isset($params['noBase']));
		return "";
	}

	public function s_gJSh($params, $content, &$smarty) {return $this->P->JSh($content);}
	public function s_gJSe($params, $content, &$smarty) {return $this->P->JSe($content);}
	public function s_gCSS($params, $content, &$smarty) {return $this->P->CSS($content);}

	public function s_gJS($params, $content, &$smarty)
	{
		return $this->P->getHead($content,
				'JavaScript'.(isset($params['name']) ? $params['name'] : 'default'),true);
	}

	public function s_oJS($params, $content, &$smarty)
	{
		return $this->P->outHead(
				'JavaScript'.(isset($params['name']) ? $params['name'] : 'default'),
				"<script type=\"text/javascript\"><!--\n", "\n--></script>");
	}

	public function s_outHead($params, &$smarty)
	{
		return $this->P->outHead(
			isset($params['name']) ? $params['name'] : 'default',
			isset($params['pre'])  ? $params['pre']  : '',
			isset($params['post']) ? $params['post'] : ''
		);
	}

	public function s_getHead($params, $content, &$smarty)
	{
		if ($content===null)
			return;
		return $this->P->getHead($content, 
			isset($params['name']) ? $params['name'] : 'default',
			isset($params['join']));
	}

}
?>
