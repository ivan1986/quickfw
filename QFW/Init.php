<?php

//регаем автолоад
require_once QFWPATH.'/QuickFW/Autoload.php';
Autoload::Init();

//определяем пути относительно известных
if (!defined('TMPPATH'))
	define('TMPPATH', VARPATH.'/tmp');
if (!defined('LOGPATH'))
	define('LOGPATH', VARPATH.'/log');
if (!defined('MODPATH'))
	define ('MODPATH', APPPATH  . '/_common/models');


class QFW
{
	/** @var array Глобальный массив данных */
	static public $globalData = array();

	/** @var QuickFW_Router Роутер */
	static public $router;

	/** @var array Конфигурация */
	static public $config;

	/** @var Templater_PlainView Шаблонизатор */
	static public $view;

	/** @var array Подключенные глобальные библиотеки */
	static public $libs = array();

	/** @var DbSimple_Database|false Подключение к базе данных */
	static public $db = false;

	/** @var mixed|false Данные о пользователе */
	static public $userdata = false;

	/** @var JsHttpRequest|jQuery|false JsHttpRequest или jQuery, если был выполнени Ajax запрос */
	static public $ajax = false;

	private function __construct() {}

	/**
	 * Инициализация конфига
	 *
	 * <br>возвращает конфигурацию, специфичную для текущего хоста
	 *
	 * @return array конфигурация на этом хосте
	 */
	static public function config()
	{
		require QFWPATH.'/config.php';
		require APPPATH.'/default.php';

		if (!isset($_SERVER['HTTP_HOST']))
			die('$_SERVER[\'HTTP_HOST\'] NOT SET');
		$file = APPPATH.'/'.$_SERVER['HTTP_HOST'].'.php';
		if (is_file($file))
			require ($file);
		return new QuickFW_Config($config, APPPATH.'/config');
	}

	/**
	 * Инициализация основных объектов QFW
	 *
	 */
	static public function Init()
	{
		self::$config = self::config();

		//выставляем заголовок с нужной кодировкой
		if (!empty(self::$config['host']['encoding']))
			header("Content-Type: text/html; charset=".self::$config['host']['encoding']);

		//Инициализируем класс базы данных
		self::$db = new DbSimple_Connect(self::$config['database']);

		//Подключаем шаблонизатор
		$templ = ucfirst(self::$config['templater']['name']);
		$class = 'Templater_'.$templ;
		require_once QFWPATH.'/Templater/'.$templ.'.php';
		self::$view = new $class(APPPATH,
			isset(self::$config['templater']['def_tpl']) ? self::$config['templater']['def_tpl'] : '');

		//подключаем модули и библиотеки
		self::modules();

		static::$router = new QuickFW_Router(APPPATH);

		//хелпер для урлов (зависит от QuickFW_Router)
		Url::Init();
	}

	/**
	 * Инициализирует необязательные модули
	 * <br>в зависимости от настроек конфигов
	 */
	static public function modules()
	{
		//Включаем обработку фатальных ошибок, если в конфиге указано
		if (!empty(self::$config['error']))
			foreach(self::$config['error'] as $handler)
				self::ErrorFromConfig($handler);

		if (self::$config['QFW']['autoload'])
			Autoload::Add(self::$config['QFW']['autoload']);

		//JsHttpRequest
		if (isset($_REQUEST['JsHttpRequest']))
		{
			self::$ajax = new JsHttpRequest(self::$config['host']['encoding']);
			//устанавливаем пустой главный шаблон
			self::$view->mainTemplate = '';
		}
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
		{
			self::$ajax = new jQuery();
			//устанавливаем пустой главный шаблон
			self::$view->mainTemplate = '';
		}
	}

	/**
	 * Вспомогательные функции инициализации библиотек
	 */

	/** @var Debug_ErrorHook_Listener Обработчик ошибок */
	static private $ErrorHook = false;
	/** @var Debug_ErrorHook_INotifier Сообщение об ошибке */
	static private $Notifer = false;

	/**
	 * Инициализация обработчика ошибок из конфига
	 *
	 * @param array $handler информация об обработчике ошибок
	 */
	static private function ErrorFromConfig($handler)
	{
		//require_once LIBPATH.'/Debug/ErrorHook/Listener.php';
		if (!self::$ErrorHook)
			self::$ErrorHook = new Debug_ErrorHook_Listener();;

		$name = ucfirst($handler['name']);
		//require_once LIBPATH.'/Debug/ErrorHook/'.$name.'Notifier.php';
		//пока так, потом возможно придется переделать
		{
			$class = 'Debug_ErrorHook_'.$name.'Notifier';
			$i = new $class(
				$handler['options']['to'], $handler['options']['whatToSend'],
				$handler['options']['subjPrefix'], $handler['options']['charset']);
		}
		if ($handler['RemoveDups'])
		{
			//require_once LIBPATH.'/Debug/ErrorHook/RemoveDupsWrapper.php';
			$i = new Debug_ErrorHook_RemoveDupsWrapper($i,
				TMPPATH.'/errors', $handler['RemoveDups']);
		}
		self::$Notifer = $i;
		self::$ErrorHook->addNotifier(self::$Notifer);
		self::$db->setErrorHandler(array(get_class(), 'dbErrorHandler'));
	}

	static public function dbErrorHandler($msg, $info)
	{
		// Если использовалась @, ничего не делать.
		if (!error_reporting()) return;
		// В лог подробную информацию об ошибке.
		list($file, $line) = explode(' line ', $info['context']);
		$msg = str_replace(' at '.$info['context'], ' of query', $msg);
		$text = "SQL Error\n\n".
			$msg."\n\n".
			$info['query']."\n";
		$trace = debug_backtrace();
		while(count($trace))
		{
			$t = $trace[0];
			if (!isset($t['file']) || $t['file']!=$file || $t['line']!=$line || $t['function'] == '__call')
			{
				array_shift($trace);
				continue;
			}
			break;
		}
		self::$Notifer->notify($info['code'], $text, $file, $line, $trace);
		exit();
	}

	/**
	 * Создание класса джаббера из конфига
	 *
	 * @return XMPPHP_XMPP класс jabbera
	 */
	static public function JabberFromConfig()
	{
		//require_once LIBPATH.'/XMPPHP/XMPP.php';
		return new XMPPHP_XMPP(
			QFW::$config['jabber']['host'], QFW::$config['jabber']['port'],
			QFW::$config['jabber']['user'], QFW::$config['jabber']['pass'],
			QFW::$config['jabber']['resource'], QFW::$config['jabber']['server'],
			!QFW::$config['QFW']['release'], XMPPHP_Log::LEVEL_INFO);
	}

}

QFW::Init();

?>
