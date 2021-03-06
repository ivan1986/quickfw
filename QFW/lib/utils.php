<?php

/**
 * Выполняет HTTP-запрос по заданному URL,
 *
 * @var string url адрес запроса
 * @var array data массив данных
 * @return string|array|false|null Возвращает: ответ сервера (без заголовков), false в случае неудачи или null в случае таймаута
 */
function getUrl($url, $data=array())
{
	$c = curl_init();
	curl_setopt_array($c, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FOLLOWLOCATION => 1,
	));
	if (isset($data['post'])) //POST запрос
	{
		curl_setopt_array($c, array(
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => is_array($data['post']) ? http_build_query($data['post']) : $data['post'],
		));
	}
	if (empty($data['file']) && !empty(QFW::$config['consts']['curlTimeOut']))
		curl_setopt($c, CURLOPT_TIMEOUT, QFW::$config['consts']['curlTimeOut']);
	if (!empty($data['sid']))
		curl_setopt($c, CURLOPT_COOKIE, 'PHPSESSID='.$data['sid']);
	if (!empty($data['user']) && !empty($data['pass']))
		curl_setopt($c, CURLOPT_USERPWD, $data['user'] . ":" . $data['pass']);
	if (!empty($data['file']))
		if (false === ($hFile = fopen($data['file'], 'w')))
			return false;
		else
			curl_setopt_array($c, array(
				CURLOPT_FILE => $hFile,
				CURLOPT_HEADER => 0,
			));
	if (isset(QFW::$config['host']['proxy']))
		curl_setopt($c, CURLOPT_PROXY, QFW::$config['host']['proxy']);

	$content = curl_exec($c);
	$code = curl_getinfo($c, CURLINFO_HTTP_CODE);
	$errno = curl_errno($c);
	curl_close($c);
	if ($errno == 28)	//CURLE_OPERATION_TIMEDOUT
		return false;
	if (!empty($data['file']))
		return fclose($hFile);
	if (array_key_exists('rcode',$data))
		return array(
			'code'=>$code,
			'content'=>$content,
		);
	if ($code >= 300)
		return false;
	return $content;
}

/**
 * Проверяет урл на корректность
 *
 * @link http://php.spb.ru/php/regexp.html
 * @param string $url Урл, который нужно проверить на корректность
 * @return false|null|string Урл или ошибка
 */
function checkUrl($url)
{
	// режем левые символы и крайние пробелы
	$url = trim(preg_replace('/[^\x20-\xFF]/', '', strval($url)));
	// если пусто - выход
	if (strlen($url)==0)
		return null;
	//проверяем УРЛ на правильность
	if (!preg_match('~^(?:(?:https?|ftp)://(?:[a-z0-9_-]{1,32}'.
	'(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|'.
	'org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?'.
	'!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-zа-я0-9.,_@%&'.
	'\[\]?+:=\~/"\'\(\)-]*)?(?:#[^ \'"&<>]*)?$~iu',$url,$ok))
		return false; // если не правильно - выход
	// если нет протокола - добавить
	if (strpos($url, '://') === false)
		$url='http://'.$url;
	return $url;
}

/**
 * Выдает несколько неповторяющихся случайных значений
 */
function n_rand($min, $max, $count)
{
	if ($max - $min + 1 < $count)
		return array();
	$a = array();
	while(count($a)<$count)
		if (!in_array($x = mt_rand($min,$max),$a))
			$a[] = $x;
	return $a;
}

/**
 * Прообразование xml в массив
 */
function xml2array($xml,$attrName='attr',$arrFlar='array')
{
	$xmlary = array();

	$reels = '/<(\w+)\s*([^>]*)\s*(?:\/>|>(.*?)<\/\s*\\1\s*>)/s';
	$reattrs = '/(\w+)=(?:"|\')([^"\']*)(?:"|\')/';

	preg_match_all($reels, $xml, $elements);

	foreach ($elements[1] as $ie => $name)
	{
		//для получения блока текста
		$cdend = strpos($elements[3][$ie], "<");
		if ($cdend > 0)
			$xmlary[$name][$ie]["text"] = substr($elements[3][$ie], 0, $cdend);

		$elements[3][$ie] = trim($elements[3][$ie]);
		if (preg_match($reels, $elements[3][$ie]))
			$xmlary[$name][$ie] = xml2array($elements[3][$ie]);
		else if ($elements[3][$ie])
			$xmlary[$name][$ie] = $elements[3][$ie];
		else
			$xmlary[$name][$ie] = null;

		if ($attributes = trim($elements[2][$ie]))
		{
			preg_match_all($reattrs, $attributes, $att);
			foreach ($att[1] as $ia => $xx)
				$xmlary[$name][$ie][$attrName][$att[1][$ia]] = $att[2][$ia];
		}

	}
	foreach ($xmlary as $k => $v)
	{
		if (count($v)==1)
			$xmlary[$k]=current($v);
		else
		{
			$xmlary[$k]=array_values($v);
			$xmlary[$k][$arrFlar]=$k;
		}
	}

	return $xmlary;
}

/**
 * Прообразование массива в xml
 */
function array2xml($array,$attrName='attr',$arrFlar='array')
{
	if(empty($array))
		return '';

	$xml=array();
	$subattr='';
	$arr=array_key_exists($arrFlar,$array)?$array[$arrFlar]:false;
	foreach ($array as $k => $v)
	{
		if ($k===$arrFlar)
			continue;
		if ($k===$attrName)
		{
			foreach ($v as $an => $av)
				$v[$an]=$an.'="'.$av.'"';
			$subattr=' '.join($v,' ');
			continue;
		}
		$k=$arr?$arr:$k;
		if (is_array($v))
		{
			$carr=array_key_exists($arrFlar,$v);
			$child=array2xml($v,$attrName);
			if (is_array($child) && !$carr)
				$xml[]='<'.$k.$child['attr'].'>'.$child['xml'].'</'.$k.'>';
			elseif ($carr)
				$xml[]=$child;
			else
				$xml[]='<'.$k.'>'.$child.'</'.$k.'>';
		}
		elseif ($v!=null)
			$xml[]='<'.$k.'>'.$v.'</'.$k.'>';
		else
			$xml[]='<'.$k.'/>';
	}
	$xml=join("\n",$xml);
	if ($subattr!='')
		return array('xml'=>$xml,'attr'=>$subattr);
	return $xml;
}

/**
 * Вызывает preg_match(_all) и
 * <br>возвращается данные в удобном формате
 * <br>с использованием флага PREG_SET_ORDER
 *
 * @param string $pattern регулярное выражение
 * @param string $subject строка для поиска
 * @param bool $all вызывать preg_match_all
 * @return array найденные паттерны
 */
function preg($pattern, $subject, $all = true)
{
	$m = array();
	if ($all)
		preg_match_all($pattern, $subject, $m, PREG_SET_ORDER);
	else
		preg_match($pattern, $subject, $m);
	return $m;
}

/**
 * Устанавливает заголовки запрещающие кешировать
 */
function nocache()
{
	header ("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
}

/**
 * Функции ескейпинга в нужной кодировке
 *
 * @param string $s Исходная строка
 * @return string htmlspecialchars($s, ENT_QUOTES, $encoding)
 */
function esc($s) { return htmlspecialchars($s, ENT_QUOTES, QFW::$config['host']['encoding']); }

/**
 * Добавляет соль пароля
 */
function password_sailt($pass)
{
  return md5($pass.QFW::$config['pass_sailt']);
}
