<?php

require_once __DIR__.'/Templater.php';
/**
 * Обертка для смарти
 *
 */
class Templater_Smarty extends Templater
{
	/** @var Smarty */
	protected $_smarty;

	/**
	 * Init - подключение и инициальзация смарти
	 * вынесено так как только по требованию
	 *
	 */
	protected function Init()
	{
		if (!is_file(LIBPATH.'/Smarty/Smarty.class.php'))
			trigger_error('Install Smarty in '.LIBPATH.'/Smarty', E_USER_ERROR);
		require LIBPATH.'/Smarty/Smarty.class.php';
		$this->_smarty = new Smarty;

		$this->_smarty->force_compile = !QFW::$config['QFW']['release'];

		$this->_smarty->compile_dir = TMPPATH . '/templates_c';
		$this->_smarty->config_dir  = TMPPATH . '/configs';
		$this->_smarty->cache_dir   = TMPPATH . '/cache';

		if (null !== $this->_tmplPath)
			$this->setScriptPath($this->_tmplPath);

		$this->_smarty->register_resource('block', array(&$this,
													"getTemplate",
													"getTimestamp",
													"isSecure",
													"isTrusted"));
		$this->regPlugs();
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
		if (!$this->_smarty)
			return true;

		$p=explode('/',$path);
		array_pop($p);
		$p=array_pop($p);
		$this->_smarty->template_dir = $path;
		$this->_smarty->compile_dir = TMPPATH . '/templates_c/'.($p!='templates'?$p:'');
		return true;
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
			$this->getEngine()->assign($spec);
		else
			$this->getEngine()->assign($spec, $value);
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
		$this->getEngine()->clear_assign($key);
	}

	/**
	 * Clear all assigned variables
	 *
	 * @return void
	 */
	public function clearVars()
	{
		$this->getEngine()->clear_all_assign();
	}

	public function getTemplateVars($var = null)
	{
		if ($var === null)
			return $this->getEngine()->get_template_vars();
		return $this->getEngine()->get_template_vars($var);
	}

	public function fetch($name, $vars=array())
	{
		$this->getEngine()->assign($vars + $this->_vars + static::$global_vars);
		$data = $this->getEngine()->fetch($name);
		return $data;
	}

	/**
	 * Return the template engine object
	 *
	 * @return Smarty
	 */
	private function getEngine()
	{
		if (!$this->_smarty)
			$this->Init();
		return $this->_smarty;
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
		$this->_smarty->register_function('baseUrl',array($this,'s_baseUrl'));
		$this->_smarty->register_function('siteUrl',array($this,'s_siteUrl'));
		$this->_smarty->register_modifier('siteUrl',array($this,'m_siteUrl'));

		$this->_smarty->register_function('addJS',array($this,'s_addJS'));
		$this->_smarty->register_function('addCSS',array($this,'s_addCSS'));

		$this->_smarty->register_function('outHead',array($this,'s_outHead'));
		$this->_smarty->register_block('getHead',array($this,'s_getHead'));
		$this->_smarty->register_function('oJS',array($this,'s_oJS'));
		$this->_smarty->register_block('gJS',array($this,'s_gJS'));
		$this->_smarty->register_block('gJSh',array($this,'s_gJSh'));
		$this->_smarty->register_block('gJSe',array($this,'s_gJSe'));
		$this->_smarty->register_block('gCSS',array($this,'s_gCSS'));

		$this->_smarty->register_function('pluralForm',array($this,'s_pluralForm'));
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
