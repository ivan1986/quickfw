<?php

/**
 * Класс для работы с сессиями и авторизацией
 *
 * @package QFW
 */
class QuickFW_Auth
{
	/** Поле логина в $_POST */
	const USERNAME_FIELD = 'login';

	/** Поле пароля в $_POST */
	const PASSWORD_FIELD = 'passw';
	
	static private $session=null;

	/** @var boolean Флаг авторизованного пользователя */
	protected $authorized;

	/** @var array Данные, которые сохраняются в сессии в подмассиве $name */
	protected $userdata;
	
	/** @var string Имя подмассива сесии в котором сохраняются данные */
	protected $name;

	/**
	 * Инициализация пользователя - авторизация или восстановление сесии
	 *
	 * @param string $name ключ в $_SESSION для хранения данных авторизации
	 * @param boolean|string $redir адрес редиректа при неудачном логине
	 * @return boolean авторизован пользователь или нет
	 */
	public function __construct($name='user',$redir=false)
	{
		//В PHP 5.3 $_REQUEST = $_GET + $_POST по умолчанию
		//Так как cookie включены почти всегда, то такой порядок оптимален
		if (isset($_COOKIE[session_name()]) || isset($_REQUEST[session_name()]))
			$this->session();
		
		$this->name=$name;
		$this->userdata = $this->authorized = false;
		if (isset($_SESSION[$name]))
		{
			$this->authorized = true;
			$this->userdata = & $_SESSION[$name];
			return true;
		}
		$this->checkPostData();
		if ($this->authorized)
			return true;
		if ($redir===false)
			return false;
		QFW::$router->route($redir);
		die();
	}
	
	/**
	 * Старт сессии
	 *
	 * <br>Если сессия уже есть ничего не делает
	 * <br>Если сессии нет, то стартует новая
	 *
	 * @param string $sid идентификатор сессии
	 */
	public function session($sid = '')
	{
		if (self::$session!=null)
			return;
		require (QFWPATH.'/QuickFW/Session.php');
		self::$session = new QuickFW_Session($sid);
	}
	
	/**
	 * Рестарт сессии
	 *
	 * <br>Если сессия уже есть - уничтожает старую и стартует новую
	 * <br>Если сессии нет, то стартует новая
	 *
	 * @param string $sid идентификатор сессии
	 */
	public function sessionRestart($sid = '')
	{
		if (self::$session!=null)
			return self::$session->restart($sid);
		require (QFWPATH.'/QuickFW/Session.php');
		self::$session = new QuickFW_Session($sid);
		return null;
	}
	
	/**
	 * Уничтожение сессии
	 *
	 * <br>Уничтожает данные сессии, стирает куки пользователя
	 */
	public function sessionDestroy()
	{
		if (self::$session==null)
			return;
		self::$session->destroy(session_id());
	}
	
	/**
	 * Проверка авторизации и сохранение данных в сессии
	 *
	 * @internal
	 * @return boolean
	 */
	private function checkPostData()
	{
		$data = $this->checkUser();
		if ($data === false)
			return; //неудачный логин
		
		$this->session();
		
		$_SESSION[$this->name] = $data;
		if (is_array($data) && array_key_exists('redirect',$data))
		{
			$r=$_SESSION[$this->name]['redirect'];
			unset($_SESSION[$this->name]['redirect']);
			QFW::$router->redirect($r===true?$_SERVER['REQUEST_URI']:$r);
		}
		$this->authorized = true;
		$this->userdata = & $_SESSION[$this->name];
	}
	
	/**
	 * Простейшая проверка авторизации - имя и пароль в конфиге
	 *
	 * <br>Это основная функция проверки авторизации
	 * <br>она перегружается в наследуемых классах
	 * <br>для авторизации на реальных проектах
	 *
	 * @global array $config['admin'] данные об авторизации пользователя
	 * @return string|false результат проверки - имя пользователя или false
	 */
	protected function checkUser()
	{
		if (!isset($_POST[self::USERNAME_FIELD]))
			return false;
		$username = isset($_POST[self::USERNAME_FIELD]) ? $_POST[self::USERNAME_FIELD] : null;
		$password = isset($_POST[self::PASSWORD_FIELD]) ? $_POST[self::PASSWORD_FIELD] : null;
		if(	(strcasecmp(QFW::$config['admin']['login'],    trim($username)) == 0)
		and	(strcasecmp(QFW::$config['admin']['passw'], trim($password)) == 0)
		)
			return $username;
		else
			return false;
	}
	
}
?>