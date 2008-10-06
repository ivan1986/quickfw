<?php

/**
 * 	Выполняет HTTP-запрос по заданному URL,
 *	используя fopen-оболочки или CURL
 *@var string url адрес запроса
 *@var string userAgent кем представляться серверу
 *@return string|false Возвращает: ответ сервера (без заголовков)или false в случае неудачи
 */
function getURLContent($url, $userAgent = 'SeriousDron Utilities Pack, getURLContent function')
{
	//Закоментил чтобы передавать ЖЖ данные бота, а это может только CURL
		if (ini_get('safe_mode') == '0' AND ini_get('allow_url_fopen') == '1') return file_get_contents($url);
		$curl = curl_init($url);

		$curlErr = curl_errno($curl);
		if ($curlErr != CURLE_OK)
		{
			curl_close($curl);
			return false;
		}

		curl_setopt($curl, CURLOPT_HEADER, false);			//заголовки не нужны
		/*curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);	//переходить по 'Location:'*/

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);	//Вернуть результат
		curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);	//Передать наш юзер-агент

		$content = curl_exec($curl);
		$curlErr = curl_errno($curl);

		curl_close($curl);

		if ($curlErr != CURLE_OK)
			return false;

		return $content;
}

/**
 * 	Вывод сообщения с разбивкой длинных слов без повреждения тегов
 */
function msg2html($s,$n=50) {

	$marker = " <> ";

	# Сохраняем все тэги чтобы уберечь их от разбивки
	preg_match_all("/(<.*?>)/si",$s,$tags);

	# Заменяем все тэги на маркеры
	$s =  preg_replace("/(<.*?>)/si", $marker, $s);

	$s = preg_replace('|\S{'.$n.'}|u','\0 ',$s);

	# Восстанавливаем тэги в места которых были отмечены маркерами
	for ($i=0; $i<count($tags[0]);  $i++)
		$s = preg_replace("/$marker/si", $tags[1][$i], $s, 1);

	return $s;
}

/**
 * 	Преобразование URL в ссылки
 */
function make_urls($string, $nofollow=false) {
    $p = '/((?:(?:ht|f)tps?:\/\/|www\.)[^<\s\n]+)(?<![]\.,:;!\})<-])/msiu';
    $r = '<a href="$1">$1</a>$2';

    $string = preg_replace($p, $r, $string);

    $p = '/ href="www\./msiu';
    $r = ' href="http://www.';

    return preg_replace($p, $r, $string);
}

/**
 * 	Печать размера файла
 */
function printSize($size)
{
	if ($size>1024*10)
		return (round($size/1024*100)/100).' Kb';
	return $size.'b';
}

/**
 * 	Усечение UTF8 строк
 */
function my_trim($srt,$size)
{
	if ((mb_strlen($srt)-3)>$size)
		$srt=mb_substr($srt,0,$size-3).'...';
	return $srt;
}


?>
