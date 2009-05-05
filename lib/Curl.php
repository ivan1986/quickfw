<?php

class Curl
{
	public $cookies = array();
	public $cookie_file = '';
	public $headers = array();
	public $options = array();
	public $referer = '';
	public $user_agent = '';
	public $proxy = '';

	protected $error = '';
	protected $clear;

	public function __construct($clear = true)
	{
		$this->user_agent  = 'Opera/10.00 (X11; Linux i686 ; U; ru) Presto/2.2.0';
		$this->proxy       = QFW::$config['host']['proxy'];
		$this->cookie_file = TMPPATH.'/curl_cookie_'.microtime(true).'.txt';
		$this->clear       = $clear;
	}

	public function __destruct()
	{
		if ($this->clear)
			unlink($this->cookie_file);
	}

	public function addCookie($name,$value)
	{
		$this->cookies[$name] = $name.'='.$value.'; path=/';
	}

	public function clearCookeis()
	{
		file_put_contents($this->cookie_file,'');
		$this->cookies = array();
	}

	public function delete($url, $vars = array())
	{
		return $this->request('DELETE', $url, $vars);
	}
	public function error()
	{
		return $this->error;
	}
	public function get($url, $vars = array())
	{
		if (!empty($vars))
		{
			$url .= (stripos($url, '?') !== false) ? '&' : '?';
			$url .= http_build_query($vars);
		}
		return $this->request('GET', $url);
	}

	public function post($url, $vars = array())
	{
		return $this->request('POST', $url, $vars);
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
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HEADER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_PROXY => $this->proxy,

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
		{
			curl_setopt($handle, constant('CURLOPT_' . str_replace('CURLOPT_', '', strtoupper($option))), $value);
		}

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

class CurlResponse
{
	public $body = '';
	public $headers = array ();

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

	public function __toString()
	{
		return $this->body;
	}
}

?>