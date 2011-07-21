<?php

require_once 'Templater.php';

class Templater_Proxy extends Templater
{

	/** @var array доступные шаблонизаторы */
	protected $templates = array();

	/**
	* Set the path to the templates
	*
	* @param string $path The directory to set as the path.
	* @return boolean
	*/
	public function setScriptPath($path)
	{
		$this->unsyncronize();
		return parent::setScriptPath($path);
	}

	/**
	 * добавляем unsyncronize
	 */
	public function assign($spec, $value = null)
	{
		$this->unsyncronize();
		parent::assign($spec, $value);
		return $this;
	}

	public function __set($name, $value)
	{
		$this->unsyncronize();
		parent::__set($name, $value);
	}

	/**
	* Clear assigned variable
	*
	* @param string|array
	* @return void
	*/
	public function delete($spec)
	{
		$this->unsyncronize();
		parent::delete($spec);
	}

	public function fetch($name, $vars=array())
	{
		$key=substr($name,strrpos($name,'.')+1);
		$T = isset(QFW::$config['templater']['exts'][$key]) ?
			QFW::$config['templater']['exts'][$key] : 'PlainView';

		if (!array_key_exists($T,$this->templates))
		{
			$templ = ucfirst($T);
			$class = 'Templater_'.$templ;
			require (QFWPATH.'/Templater/'.$templ.'.php');
			//Подключить класс шаблонизатора
			$this->templates[$T] = array(
				'c' => new $class(APPPATH,''),
				's' => true,
			);
			$this->syncronize($this->templates[$T]['c']);
		}
		elseif (!$this->templates[$T]['s'])
		{
			$this->syncronize($this->templates[$T]['c']);
			$this->templates[$T]['s']=true;
		}
		return $this->templates[$T]['c']->fetch($name, $vars);
	}

	/**
	 * Сброс флага синхронизации
	 */
	protected function unsyncronize()
	{
		foreach ($this->templates as $k=>$v)
			$this->templates[$k]['s']=false;
	}

	/**
	 * Синхронизация проксируемого шаблонизатора
	 *
	 * @param Templater $tpl шаблонизатор
	 */
	protected function syncronize($tpl)
	{
		$tpl->mainTemplate=$this->mainTemplate;
		$tpl->setScriptPath($this->_tmplPath);
		$tpl->clearVars();
		$tpl->assign($this->_vars);
	}

}
?>