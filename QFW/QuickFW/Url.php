<?php

/**
 * Хелпер для адресов
 *
 * @author ivan1986
 */
class Url
{
	/**
	 * Добавляет в начале базовый урл
	 *
	 * @param string|self $url url
	 * @return self базовый url
	 */
	public static function base($url='')
	{
		return new self('').$url;
	}

	/**
	 * Урл, относительно корня сайта
	 *
	 * @param string|self $url url
	 * @param string|array $get параметры
	 * @param string $anchor якорь
	 * @return self адрес на сайте
	 */
	public static function site($url='', $get='', $anchor='')
	{
		return new self($url, $get, $anchor);
	}

	/**
	 * Урл, относительно модуля
	 *
	 * @param string|self $CA url
	 * @param string|array $get параметры
	 * @param string $anchor якорь
	 * @return self адрес на сайте
	 */
	public static function M($CA='', $get='', $anchor='')
	{
		return new self($CA, $get, $anchor, QFW::$router->cModule.
			QuickFW_Router::PATH_SEPARATOR);
	}

	/**
	 * Урл, относительно контроллера
	 *
	 * @param string|self $action url
	 * @param string|array $get параметры
	 * @param string $anchor якорь
	 * @return self адрес на сайте
	 */
	public static function C($action='', $get='', $anchor='')
	{
		return new self($action, $get, $anchor, QFW::$router->cModule.
				QuickFW_Router::PATH_SEPARATOR.
			QFW::$router->cController.
				QuickFW_Router::PATH_SEPARATOR);
	}

	/**
	 * Урл, относительно экшена
	 *
	 * @param string|self $params url
	 * @param string|array $get параметры
	 * @param string $anchor якорь
	 * @return self адрес на сайте
	 */
	public static function A($params='', $get='', $anchor='')
	{
		return new self($params, $get, $anchor, QFW::$router->cModule.
				QuickFW_Router::PATH_SEPARATOR.
			QFW::$router->cController.
				QuickFW_Router::PATH_SEPARATOR.
			QFW::$router->cAction.QuickFW_Router::PATH_SEPARATOR);
	}

	/**
	 * Инициализация класса из конфига
	 */
	public static function Init()
	{
		self::$config = QFW::$config['redirection'];
		self::$config['base'] = self::$config['baseUrl'].
			(self::$config['useIndex'] ? 'index.php/' : '');
		self::$config['ext'] = self::$config['defExt'] ? self::$config['defExt'] :
			(QuickFW_Router::PATH_SEPARATOR == '/' ? '/' : '');
	}

	/** @var array QFW::$config['redirection'] */
	private static $config;

	/**
	 * Конструктор класса запроса
	 *
	 * @param string $url внутреннее представление адреса
	 * @param string|array $get параметры get
	 * @param string $anchor якорь
	 * @param string $begin базовый урл от текущего
	 */
	private function __construct($url, $get='', $anchor='', $begin='')
	{
		if (is_array($get) && count($get))
			$get = http_build_query($get);
		if ($url instanceof self)
		{
			$this->u = $begin.$url->u;
			$this->get = $url->get.($get?('&'.$get):'');
			$this->anchor = $anchor ? ltrim($anchor, '#') : $url->anchor;
			return;
		}
		//Заменяем / на QuickFW_Router::PATH_SEPARATOR
		if (QuickFW_Router::PATH_SEPARATOR != '/')
			$url = strtr($url, '/', QuickFW_Router::PATH_SEPARATOR);
		$this->u = trim($begin.$url, QuickFW_Router::PATH_SEPARATOR);
		$this->get = $get;
		$this->anchor = ltrim($anchor, '#');
		if (self::$config['delDef'])
			$this->u = QFW::$router->delDef($this->u);
	}

	/** @var string внутреннее представление адреса */
	private $u;

	/** @var string внутреннее представление адреса */
	private $get;

	/** @var string якорь */
	private $anchor;

	/**
	 * урл для вывода, с подстановками
	 *
	 * @return string урл
	 */
	public function  __toString()
	{
		return $this->get();
	}

	/**
	 * урл для вывода, с подстановками
	 *
	 * @return string урл
	 */
	public function get()
	{
		return QFW::$router->backrewriteUrl(
			self::$config['base'].QFW::$router->backrewrite($this->u).
			($this->u!=='' ? self::$config['ext'] : '').
			($this->get ? '?' . $this->get : '').
			($this->anchor ? '#' . $this->anchor : ''));
	}

	/**
	 * Внутренний адрес - для блока
	 *
	 * @internal
	 * @return string внутренний адрес
	 */
	public function intern()
	{
		return $this->u;
	}

}

?>
