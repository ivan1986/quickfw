<?php

/**
 * Общий предок шаблонов
 */
abstract class Templater
{
	protected static $global_vars = array();
	/** @var array переменные, установленные в шаблоне */
	protected $_vars = array();
	/** @var string путь к шаблонам */
	protected $_tmplPath;

	/** @var QuickFW_Plugs Плагины фреймворка
	 *  @deprecated Используйте Hlp
	 */
	public $P;
	
	/** @var String Основной шаблон (путь относительно директории шаблонов) */
	public $mainTemplate;

	public function __construct($tplPath, $mainTpl)
	{
		$this->_vars = array();
		$this->_tmplPath = $tplPath;
		$this->P = QuickFW_Plugs::getInstance();
		$this->mainTemplate = $mainTpl;
	}

	public function __get($name)
	{
		return $this->getTemplateVars($name);
	}

	public function __set($name, $value)
	{
		$this->_vars[$name] = $value;
	}

	/**
	 * Sets a view variable.
	 *
	 * @param   string|array  name of variable or an array of variables
	 * @param   mixed         value when using a named variable
	 * @return  object
	 */
	public function set($name, $value = NULL)
	{
		if (is_array($name))
			$this->_vars = array_merge($this->_vars, $name);
		else
			$this->__set($name, $value);
		return $this;
	}

	/**
	 * Функция для совместимости с kohana View
	 *
	 * @static
	 * @param $name
	 * @param null $value
	 * @return void
	 */
	public static function set_global($name, $value = NULL)
	{
		if (is_array($name))
			static::$global_vars = array_merge(static::$global_vars, $name);
		else
			static::$global_vars[$name] = $value;
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
		self::set_global($name, $value);
		return $this;
	}

	/**
	 * Добавлеет в массив в шаблонизаторе новое значение
	 *
	 * @param string $name имя массива
	 * @param mixed $value значение
	 */
	public function append($name, $value)
	{
		if (empty(static::$global_vars[$name]))
			static::$global_vars[$name] = array();
		static::$global_vars[$name][] = $value;
		return $this;
	}

	/**
	 * Удаляет указанную переменную из шаблона
	 *
	 * @param string|array имя переменной или массив имен
	 */
	public function delete($spec)
	{   //TODO: уделание из всех
		if (is_array($spec))
			foreach ($spec as $item)
				unset($this->_vars[$item]);
		else
			unset($this->_vars[$spec]);
	}

	/**
	 * Sets a bound variable by reference.
	 *
	 * @param   string   name of variable
	 * @param   mixed    variable to assign by reference
	 * @return  object
	 */
	public function bind($name, & $var)
	{
		$this->_vars[$name] =& $var;
		return $this;
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
	{   //TODO: получение из всех
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
		return call_user_func_array(array(&QFW::$router, 'blockRoute'), func_get_args());
	}

	/**
	 * Вызывает выполнение блока и возвращает результат
	 *
	 * @param string $ns неймспейс из которого вызвать (должен быть инициализирован)
	 * @param string $block имя блока (MCA)
	 * @return string результат работы блока
	 */
	public function localBlock($ns, $block)
	{
		$args = func_get_args();
		array_shift($args);
		$c = $ns.'\QFW';
		return call_user_func_array(array($c::$router, 'blockRoute'), $args);
	}

	/**
	 * Синоним fetch
	 */
	public function render($tmpl, $vars=array())
	{
		return $this->fetch($tmpl, $vars);
	}

	abstract public function fetch($tmpl, $vars=array());

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