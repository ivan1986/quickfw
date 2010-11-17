<?php

require_once 'Helpers.php';

class QuickFW_Plugs
{
	protected static $_thisInst = null;

	protected function __construct()
	{
	}

	public static function getInstance()
	{
		if (self::$_thisInst === null)
			self:: $_thisInst = new QuickFW_Plugs();
		return self::$_thisInst;
	}

	public function baseUrl()
	{
		trigger_error('Используйте Url::base()', E_USER_NOTICE);
		return QFW::$config['redirection']['baseUrl'];
	}

	public function siteDefUrl($url, $get='')
	{
		return $this->siteUrl(QFW::$router->delDef($url), $get);
	}

	/**
	 * @deprecated Используйте Url::...
	 */
	public function siteUrl($url, $get='')
	{
		trigger_error('Используйте Url::...', E_USER_NOTICE);
		if (QFW::$config['redirection']['delDef'])
			$url = QFW::$router->delDef($url);
		if (QFW::$config['redirection']['useRewrite'])
			$url = QFW::$router->backrewrite($url);
		if (is_array($get) && count($get))
			$get = '?'.http_build_query($get);
		return QFW::$config['redirection']['baseUrl'].
			(QFW::$config['redirection']['useIndex']?'index.php/':'').
			$url.
			($url!==''?QFW::$config['redirection']['defExt']:'').$get;
	}

	public function addJS($file, $noBase=false)
	{
		Helpers::addJS($file, $noBase);
		return "";
	}

	public function addCSS($file, $noBase=false)
	{
		Helpers::addCSS($file, $noBase);
		return "";
	}

	//Cтандартные вставки - JS в начало и в конец документа и CSS в начало
	public function JSh($data) {return Helpers::JSh($data);}
	public function JSe($data) {return Helpers::JSe($data);}
	public function CSS($data) {return Helpers::CSS($data);}
	public function sJSh() {return Helpers::sJSh();}
	public function eJSh() {return Helpers::eJSh();}
	public function sJSe() {return Helpers::sJSe();}
	public function eJSe() {return Helpers::eJSe();}
	public function sCSS() {return Helpers::sCSS();}
	public function eCSS() {return Helpers::eCSS();}

	//сокращения для JavaScript
	public function sJS($name='') {return Helpers::sJS($name);}
	public function eJS($name='') {return Helpers::eJS($name);}
	public function oJS($name='') {return Helpers::oJS($name);}

	public function outHead($name='default', $pre='',$post='')
	{
		return Helpers::outHead($name, $pre, $post);
	}

	public function getHead($content, $name='default', $join=false)
	{
		Helpers::getHead($content, $name, $join);
	}

	/**
	 * Отображение сообщений об ошибках
	 */
	public function displayErrors($errors=array())
	{
		return Helpers::displayErrors($errors);
	}

	/**
	 * Установка обрамления сообщений об ошибках
	 */
	public function setDisplayErrorsParams($pre='', $post='')
	{
		Helpers::setDisplayErrorsParams($pre, $post);
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

?>