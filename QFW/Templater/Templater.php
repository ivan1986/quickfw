<?php

/**
 * Общий предок шаблонов
 */
abstract class Templater
{
	/** @var array переменные, установленные в шаблоне */
	protected $_vars;
	/** @var string путь к шаблонам */
	protected $_tmplPath;

	/** @var QuickFW_Plugs Плагины фреймворка
	 *  @deprecated Используйте Hlp
	 */
	public $P;
	
	/** @var String Основной шаблон (путь относительно директории шаблонов) */
	public $mainTemplate;

	public function __construct($tmplPath, $mainTmpl)
	{
		$this->_vars = array();
		$this->_tmplPath = $tmplPath;
		$this->P = QuickFW_Plugs::getInstance();
		$this->mainTemplate = $mainTmpl;
	}

	/**
	 * Присваение значения переменной шаблона
	 *
	 * Allows setting a specific key to the specified value, OR passing an array
	 * of key => value pairs to set en masse.
	 *
	 * @param string|array $name Имя переменной или массив (ключ => значение)
	 * @param mixed $value Значение переменной, если первый параметр не массив
	 * @return Templater_PlainView Ссылка на себя
	 */
	public function assign($name, $value = null)
	{
		if (is_array($name))
			$this->_vars = array_merge($this->_vars, $name);
		else
			$this->_vars[$name] = $value;
		return $this;
	}

	/**
	 * Удаляет указанную переменную из шаблона
	 *
	 * @param string|array имя переменной или массив имен
	 */
	public function delete($spec)
	{
		if (is_array($spec))
			foreach ($spec as $item)
				unset($this->_vars[$item]);
		else
			unset($this->_vars[$spec]);
	}

	/**
	 * Очистка всех установленных переменных
	 *
	 */
	public function clearVars()
	{
		$this->_vars=array();
	}

	/**
	 * Возвращает значения переменных в шаблоне
	 *
	 * @param string $var имя переменной
	 * @return mixed значение
	 */
	public function getTemplateVars($var = null)
	{
		if ($var === null)
			return $this->_vars;
		elseif (isset($this->_vars[$var]))
			return $this->_vars[$var];
		else
			return null;
	}

	/**
	 * Возвращает путь к шаблонам
	 *
	 * @return string путь
	 */
	public function getScriptPath()
	{
		return $this->_tmplPath;
	}

	/**
	 * Устанавливает путь к шаблонам
	 *
	 * @param string $path новый путь
	 * @return boolean корректный ли путь
	 */
	public function setScriptPath($path)
	{
		if (!is_readable($path))
			return false;
		$this->_tmplPath = $path;
		return true;
	}

	/**
	 * Вызывает выполнение блока и возвращает результат
	 *
	 * @param string $block имя блока (MCA)
	 * @return string результат работы блока
	 */
	public function block($block)
	{
		//TODO: убрать ненужную переменную после перехода на php 5.3
		$args = func_get_args();
		return call_user_func_array(array(&QFW::$router, 'blockRoute'), $args);
	}

	/**
	 * Синоним fetch
	 */
	public function render($tmpl)
	{
		return $this->fetch($tmpl);
	}

	abstract public function fetch($tmpl);

	/**
	 * Генерирует полный вывод, обрабатывает фильтрами
	 *
	 * @param <type> $content основной контент страницы
	 * @return string вся страница с главным шаблоном
	 * @internal
	 */
	public function displayMain($content)
	{
		if (isset($this->mainTemplate) && $this->mainTemplate!="")
		{
			//Необходимо для установки флага CSS
			Hlp::startDisplayMain();
			$this->assign('content',$content);
			$content = $this->render($this->mainTemplate);
		}
		//Необходимо для вызовов всех деструкторов
		QFW::$router->startDisplayMain();
		return Hlp::HeaderFilter($content);
	}

	/**
	 * Функции ескейпинга в нужной кодировке
	 *
	 * @param string $s Исходная строка 
	 * @return string htmlspecialchars($s, ENT_QUOTES, $encoding)
	 */
	public function esc($s)
	{
		return htmlspecialchars($s, ENT_QUOTES,
			QFW::$config['host']['encoding']);
	}

}

/**
 * Класс для сохранения состояния шаблонизатора<br>
 * Сохраняет путь к шаблонам и переменные
 *
 * @author ivan1986
 */
class TemplaterState
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