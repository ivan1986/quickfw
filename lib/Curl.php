<?php

/**
 * Класс для обхода других сайтов как браузер
 * Авторизация, корректная работа с куками, прочие навороты
 */
class Curl
{
	/** @var array Массив кук */
	public $cookies = array();
	/** @var string файл, в котором хранятся куки */
	public $cookie_file = '';
	/** @var boolean сохранять файл с куками постоянно */
	public $persist_cookie_file = false;
	/** @var array массив заголовков запроса */
	public $headers = array();
	/** @var array массив дополнительных опций Curl */
	public $options = array();
	/** @var string заголовок referer */
	public $referer = '';
	/** @var string заголовок USER_AGENT */
	public $user_agent = '';
	/** @var string адрес прокси */
	public $proxy = '';
	/** @var integer таймаут в секундах */
	public $timeout = 0;

	protected $error = '';
	protected $clear;

	public function __construct($cookieFile='')
	{
		$this->user_agent  = 'Opera/10.00 (X11; Linux i686 ; U; ru) Presto/2.2.0';
		$this->proxy       = isset(QFW::$config['host']['proxy']) ? QFW::$config['host']['proxy'] : '';
		if ($cookieFile)
			$this->persist_cookie_file = true;
		else
			$cookieFile = tempnam(sys_get_temp_dir(), 'curl_cookie');
		$this->cookie_file = $cookieFile;
	}

	public function __destruct()
	{
		if (!$this->persist_cookie_file)
			unlink($this->cookie_file);
	}

	/**
	 * Добавляет новую куку
	 *
	 * @param string $name имя
	 * @param string $value значение
	 */
	public function addCookie($name,$value)
	{
		$this->cookies[$name] = $name.'='.$value.'; path=/';
	}

	/**
	 * Очистка печенек
	 */
	public function clearCookeis()
	{
		file_put_contents($this->cookie_file,'');
		$this->cookies = array();
	}

	/**
	 * Возвращает последнюю ошибку
	 *
	 * @return string строка ошибки
	 */
	public function error()
	{
		return $this->error;
	}
	
	/**
	 * GET запрос по адресу
	 * 
	 * @param string $url - адрес по которому делать запрос
	 * @param array $vars - дополнительные переменные в get
	 * @return CurlResponse - результат
	 */
	public function get($url, $vars = array())
	{
		if (!empty($vars))
		{
			$url .= (stripos($url, '?') !== false) ? '&' : '?';
			$url .= http_build_query($vars);
		}
		return $this->request('GET', $url);
	}

	/**
	 * POST запрос по адресу
	 *
	 * @param string $url - адрес по которому делать запрос
	 * @param array $vars - переменные в post
	 * @return CurlResponse - результат
	 */
	public function post($url, $vars = array())
	{
		return $this->request('POST', $url, $vars);
	}
	
	public function delete($url, $vars = array())
	{
		return $this->request('DELETE', $url, $vars);
	}
	
	public function put($url, $vars = array())
	{
		return $this->request('PUT', $url, $vars);
	}
	
	protected function request($method, $url, $vars = array())
	{
		$handle = curl_init();

		//Set some default CURL options
		curl_setopt_array($handle, array(
			CURLOPT_AUTOREFERER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HEADER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_PROXY => $this->proxy,

			CURLOPT_CONNECTTIMEOUT => $this->timeout,
			CURLOPT_TIMEOUT => $this->timeout,

			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0,

			CURLOPT_USERAGENT => $this->user_agent,
			CURLOPT_REFERER => $this->referer,

			CURLOPT_URL => $url,
		));
		curl_setopt($handle, CURLOPT_COOKIEFILE, $this->cookie_file);
		curl_setopt($handle, CURLOPT_COOKIEJAR, $this->cookie_file);

		//Format custom headers for this request and set CURL option
		$headers = array ();
		foreach ( $this->headers as $key => $value )
		{
			$headers[] = $key . ': ' . $value;
		}
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($handle, CURLOPT_COOKIE, join('; ',$this->cookies));
		//Determine the request method and set the correct CURL option
		switch ($method)
		{
		case 'GET' :
			curl_setopt($handle, CURLOPT_HTTPGET, true);
			break;
		case 'POST' :
			curl_setopt_array($handle, array(
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => is_array($vars)?http_build_query($vars):$vars,
			));
			break;
		default :
			curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
		}

		//Set any custom CURL options
		foreach ( $this->options as $option => $value )
			curl_setopt($handle, constant('CURLOPT_' .
				str_replace('CURLOPT_', '', strtoupper($option))), $value);

		$response = curl_exec($handle);
		$header_size = curl_getinfo($handle,CURLINFO_HEADER_SIZE);
		$result['header'] = substr($response, 0, $header_size);
		$result['body'] = substr($response, $header_size);
		$result['http_code'] = curl_getinfo($handle,CURLINFO_HTTP_CODE);
		$result['last_url'] = curl_getinfo($handle,CURLINFO_EFFECTIVE_URL);

		if ($response)
		{
			$response = new CurlResponse($result);
			$this->referer = $result['last_url'];
		}
		else
		{
			$this->error = curl_errno($handle) . ' - ' . curl_error($handle);
		}
		curl_close($handle);
		return $response;
	}
}

/**
 * Класс ответа сервера - содержит хедеры и дополнительные навороты
 */
class CurlResponse
{
	/** @var string тело ответа */
	public $body = '';
	
	/** @var array заголовки ответа */
	public $headers = array ();

	/** @var boolean Флаг для перекодировки */
	private $win=false;

	public function __construct($response)
	{
		$this->headers = $response;
		$this->body = $response['body'];
		unset($this->headers['body']);

		$this->headers['header'] = explode("\n", $this->headers['header']);
		foreach ( $this->headers['header'] as $v )
			if (($p=strpos($v,':'))!==false)
				$this->headers[trim(substr($v,0,$p))]=trim(substr($v,$p+1));
		unset($this->headers['header']);

		//загзипленные страницы
		if (isset($this->headers['Content-Encoding']) && $this->headers['Content-Encoding'] == 'gzip')
		{	//функция gzuncompress не работает (по крайне мере в PHP 5.2.6)
			file_put_contents(TMPPATH.'/a', $this->body);
			system('cat '.TMPPATH.'/a | gunzip | cat > '.TMPPATH.'/b');
			$this->body = file_get_contents(TMPPATH.'/b');
		}
	}

	/**
	 * Преобразовывает запрос из Win1251 в UTF-8
	 * 
	 * <br>Очень часто требуется, так как виндузятников развелось немеряно
	 * 
	 * @return string преобразованная строка
	 */
	public function fromWin()
	{
		if ($this->win)
			return $this->body;
		$this->win = true;
		return $this->body = mb_convert_encoding($this->body,'utf-8','cp1251');
	}

	/**
	 * Просто тело запроса
	 * 
	 * @return string результат запроса
	 */
	public function __toString()
	{
		return $this->body;
	}
	
	/**
	 * Формы на странице
	 * 
	 * @param int $num - номер формы на странице
	 * @return array - массив с формами в каждой поля со значениями
	 */
	public function forms($num=false)
	{
		$m = array();
		preg_match_all('|<form(.*?)>.*?</form>|si',$this->body,$m, PREG_SET_ORDER);
		if (count($m)==0)
			return false;
		$forms = array();
		foreach($m as $k=>$f)
		{
			if ($num!==false && $num!=$k)
				continue;
			$forms[$k] = $this->parceTagParams($f[1]);
			$fields = array();
			preg_match_all('#<(input|textarea|select).*?>#si',$f[0],$m);
			foreach ($m[0] as $field)
			{
				$r = $this->parceTagParams($field);
				if (isset($r['name']))
					if (($p = strpos($r['name'], '[]')) === false)
						$fields[$r['name']] = isset($r['value'])?$r['value']:null;
					else
						$fields[substr($r['name'], 0, $p)][] = isset($r['value'])?$r['value']:null;
			}
			$forms[$k]['fields'] = $fields;
		}
		return $num===false ? $forms : $forms[$num];
	}

	/**
	 * Извлекает параметры из тега
	 *
	 * @param string $str тег
	 * @return array массив параметров
	 */
	protected function parceTagParams($str)
	{
		$t = $x = array();
		preg_match_all('|([a-z]+)=([\'"]?)(.*?)\2|si', $str, $t);
		for($i=0; $i<count($t[1]); $i++)
			$x[$t[1][$i]] = $t[3][$i];
		return $x;
	}

}

?>