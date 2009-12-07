<?php

/**
 * Класс для сохранения состояния шаблонизатора<br>
 * Сохраняет путь к шаблонам и переменные
 *
 * @author ivan1986
 */
class Templater_State
{
	/** @var string текущий путь к шаблонам */
	private $path;

	/** @var array Массив переменных */
	private $vars;

	/** @var Templater_PlainView Ссылка на шаболонизатор */
	private $tpl;

	/**
	 * Сохраняет все переменные в шаблоне, а при уничтожении восстанавливает
	 *
	 * @param Templater_PlainView $templater
	 */
	public function  __construct($templater)
	{
		$this->path = $templater->getScriptPath();
		$this->vars = $templater->getTemplateVars();
		$this->tpl = $templater;
	}

	/**
	 * Восстанавливает старые переменные
	 */
	public function  __destruct()
	{
		$this->tpl->setScriptPath($this->path);
		$this->tpl->clearVars();
		$this->tpl->assign($this->vars);
	}

};

?>
