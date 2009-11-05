<?php
/**
 * Отправка Email С аттачами и в html формате
 *
 * @author ivan1986
 */
class Email
{
	/** @var string Поле от кого */
	private $from = '';
	/** @var string Поле скрытая копия */
	private $cc = '';
	/** @var string Поле Replay */
	private $replay = '';
	/** @var string Тема письма */
	private $subject = '';
	/** @var string Простой текст */
	private $plain = '';
	/** @var string Html текст */
	private $html = '';
	/** @var array[string]mixed Прикрепленные файлы */
	private $files = array();

	/** @var string Сообщение (заполняется функцией build) */
	private $message = '';
	/** @var string Заголовки (заполняется функцией build) */
	private $headers = '';

	public function __construct()
	{
		
	}

	/**
	 * Устанавливает поле From
	 *
	 * @param string $email e-mail
	 * @param string $name Имя в utf8, которое будет закодировано в base64 или пусто
	 * @return Email Указатель на себя
	 */
	public function setFrom($email, $name='')
	{
		$this->message = $this->headers = '';
		$this->from = empty($name) ? $email : '=?utf-8?B?'.base64_encode($name).'?= <'.$email.'>';
		return $this;
	}

	/**
	 * Устанавливает поле Cc
	 *
	 * @param string $email e-mail
	 * @param string $name Имя в utf8, которое будет закодировано в base64 или пусто
	 * @return Email Указатель на себя
	 */
	public function setCc($email, $name='')
	{
		$this->message = $this->headers = '';
		$this->cc = empty($name) ? $email : '=?utf-8?B?'.base64_encode($name).'?= <'.$email.'>';
		return $this;
	}

	/**
	 * Устанавливает поле Subject
	 *
	 * @param string $subject Тема письма в utf8
	 * @return Email Указатель на себя
	 */
	public function setSubject($subject)
	{
		$this->message = $this->headers = '';
		$this->subject = '=?utf-8?B?'.base64_encode($subject).'?=';
		return $this;
	}
	
	/**
	 * Устанавливает поле Replay To
	 *
	 * @param string $email e-mail
	 * @param string $name Имя в utf8, которое будет закодировано в base64 или пусто
	 * @return Email Указатель на себя
	 */
	public function setReplay($email, $name='')
	{
		$this->message = $this->headers = '';
		$this->replay = empty($name) ? $email : '=?utf-8?B?'.base64_encode($name).'?= <'.$email.'>';
		return $this;
	}

	/**
	 * Устанавливает Текст письма в формате html
	 *
	 * @param string $html Верстка письма в utf8
	 * @return Email Указатель на себя
	 */
	public function setHtml($html)
	{
		$this->message = $this->headers = '';
		$this->html = $html;
		return $this;
	}

	/**
	 * Устанавливает Текст письма в plain text формате
	 *
	 * @param string $plain Простой текст письма в utf8
	 * @return Email Указатель на себя
	 */
	public function setPlain($plain)
	{
		$this->message = $this->headers = '';
		$this->plain = $plain;
		return $this;
	}

	/**
	 * Добавляет файл к письму с указанным идентификатором
	 *
	 * @param string $name Идентификатор файла
	 * @param mixed $data Содержимое файла
	 * @param string $type mime-type содержимого
	 * @return Email Указатель на себя
	 */
	public function addFile($name, $data, $type)
	{
		$this->message = $this->headers = '';
		$this->files[$name] = array(
			'data' => $data,
			'type' => $type,
		);
		return $this;
	}

	/**
	 *
	 * @param string|array[]string|array[string]string $to Адреса получателей<br>
	 * Строка - email | Хеш (Ключ - email, значение - имя) | Массив хешей
	 * @param boolean $join Одно письмо с множеством адресов в to
	 * @return Email Указатель на себя
	 */
	public function send($to, $join = false)
	{
		//Сообщение изменилось - переформируем
		if (empty($this->message))
		{
			if (!empty($this->html))
				$this->prepareHtml();
			elseif(!empty($this->files))
				$this->prepareVsAttache();
			else
				$this->prepare();
		}

		if(!is_array($to))
			$to = array($to);
		foreach($to as $k=>$v)
			if (!is_int($k))
				$to[$k] = '=?utf-8?B?'.base64_encode($v).'?= <'.$k.'>';
		$to = array_values($to);

		if ($join)
		{
			$to = join($to, ', ');
			mail($to, $this->subject, $this->message, $this->headers);
		}
		else
			foreach($to as $mail)
				mail($mail, $this->subject, $this->message, $this->headers);
		return $this;
	}

	/**
	 * Возвращает массив из raw данных, которые подставляются в mail
	 *
	 * @return array Хеш subject, message, headers
	 */
	public function getRaw()
	{
		//Сообщение изменилось - переформируем
		if (empty($this->message))
		{
			if (!empty($this->html))
				$this->prepareHtml();
			elseif(!empty($this->files))
				$this->prepareVsAttache();
			else
				$this->prepare();
		}
		return array(
			'subject' => $this->subject,
			'message' => $this->message,
			'headers' => $this->headers,
		);
	}

	/**
	 * Собирает сообщение в html формате
	 *
	 * @uses headers
	 * @uses message
	 */
	private function prepareHtml()
	{
		$baseboundary = "------------".strtoupper(md5(uniqid('base')));
		$newboundary  = "------------".strtoupper(md5(uniqid('new')));

		$headers = '';
		$message = '';
		if ($this->from)	$headers.='From: '.$this->from."\r\n";
		if ($this->cc)		$headers.='Cc: '.$this->cc."\r\n";
		if ($this->replay)	$headers.='Reply-To: '.$this->replay."\r\n";
		if ($this->subject) $headers.='Subject: '.$this->subject."\r\n";

		$headers.='Date: '.date('r')."\r\n";
		$headers.="X-Mailer: php script\r\n";
		$headers.="MIME-Version: 1.0\r\n";
		$headers.="Content-Type: multipart/alternative;\r\n";
		$headers.="  boundary=\"".$baseboundary."\"\r\n";
		$headers.="This is a multi-part message in MIME format.\r\n";

		$message.="--".$baseboundary."\r\n";
		$message.="Content-Type: text/plain; charset=utf-8\r\n";
		$message.="Content-Transfer-Encoding: 8bit\r\n\r\n";
		$message.=$this->plain."\r\n\r\n";
		$message.="--".$baseboundary."\r\n";


		$message.="Content-Type: multipart/related;\r\n";
		$message.="  boundary=\"$newboundary\"\r\n\r\n\r\n";
		$message.="--$newboundary\r\n";
		$message.="Content-Type: text/html; charset=utf-8\r\n";
		$message.="Content-Transfer-Encoding: 8bit\r\n\r\n";
		$message.=$this->html."\r\n\r\n";

		if (count($this->files))
			foreach($this->files as $name => $file)
			{
				$message.="--".$newboundary."\r\n";
				$message.="Content-Type: ".$file['type'].";\r\n";
				$message.=" name=\"".$name."\"\r\n";
				$message.="Content-Transfer-Encoding: base64\r\n";
				$message.="Content-ID: <".$name.">\r\n";
				$message.="Content-Disposition: inline;\r\n";
				$message.=" filename=\"".$name."\"\r\n\r\n";
				$message.=chunk_split(base64_encode($file['data']));
			}

		$message.="--".$newboundary."--\r\n\r\n";
		$message.="--".$baseboundary."--\r\n";

		$this->headers = $headers;
		$this->message = $message;
	}

	/**
	 * Собирает сообщение в текстовом формате с аттачами
	 *
	 * @uses headers
	 * @uses message
	 */
	private function prepareVsAttache()
	{
		$boundary = "------------".strtoupper(md5(uniqid('file')));

		$headers = '';
		$message = '';
		if ($this->from)	$headers.='From: '.$this->from."\r\n";
		if ($this->cc)		$headers.='Cc: '.$this->cc."\r\n";
		if ($this->replay)	$headers.='Reply-To: '.$this->replay."\r\n";
		if ($this->subject) $headers.='Subject: '.$this->subject."\r\n";

		$headers.='Date: '.date('r')."\r\n";
		$headers.="X-Mailer: php script\r\n";
		$headers.="MIME-Version: 1.0\r\n";
		$headers.="Content-Type: multipart/mixed;\r\n";
		$headers.="  boundary=\"".$boundary."\"\r\n";
		$headers.="This is a multi-part message in MIME format.\r\n";

		$message.="--".$boundary."\r\n";
		$message.="Content-Type: text/plain; charset=utf-8\r\n";
		$message.="Content-Transfer-Encoding: 8bit\r\n\r\n";
		$message.=$this->plain."\r\n\r\n";
		$message.="--".$boundary."\r\n";

		if (count($this->files))
			foreach($this->files as $name => $file)
			{
				$message.="--".$boundary."\r\n";
				$message.="Content-Type: ".$file['type'].";\r\n";
				$message.=" name=\"".$name."\"\r\n";
				$message.="Content-Transfer-Encoding: base64\r\n";
				$message.="Content-ID: <".$name.">\r\n";
				$message.="Content-Disposition: inline;\r\n";
				$message.=" filename=\"".$name."\"\r\n\r\n";
				$message.=chunk_split(base64_encode($file['data']));
			}

		$message.="--".$boundary."--\r\n\r\n";

		$this->headers = $headers;
		$this->message = $message;
	}

	/**
	 * Собирает сообщение в текстовом формате
	 *
	 * @uses headers
	 * @uses message
	 */
	private function prepare()
	{
		$boundary = "------------".strtoupper(md5(uniqid('')));

		$headers = '';
		$message = '';
		if ($this->from)	$headers.='From: '.$this->from."\r\n";
		if ($this->cc)		$headers.='Cc: '.$this->cc."\r\n";
		if ($this->replay)	$headers.='Reply-To: '.$this->replay."\r\n";
		if ($this->subject) $headers.='Subject: '.$this->subject."\r\n";

		$headers.='Date: '.date('r')."\r\n";
		$headers.="X-Mailer: php script\r\n";
		$headers.="MIME-Version: 1.0\r\n";
		$headers.="Content-Type: text/plain; charset=utf-8\r\n";
		$headers.="Content-Transfer-Encoding: 8bit\r\n\r\n";
		$message.=$this->plain."\r\n\r\n";

		$this->headers = $headers;
		$this->message = $message;
	}

}

?>
