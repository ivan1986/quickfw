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
		$this->unsyncronize();
		parent::assign($spec, $value);
		return $this;
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

	public function fetch($name)
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
		return $this->templates[$T]['c']->fetch($name);
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