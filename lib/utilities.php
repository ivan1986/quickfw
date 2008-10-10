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

/**
 * Прообразование xml в массив
 */
function xml2array($xml,$attrName='attr',$arrFlar='array')
{
	$xmlary = array();

	$reels = '/<(\w+)\s*([^>]*)\s*(?:\/>|>(.*?)<\/\s*\\1\s*>)/s';
	$reattrs = '/(\w+)=(?:"|\')([^"\']*)(?:"|\')/';

	preg_match_all($reels, $xml, $elements);

	foreach ($elements[1] as $ie => $xx)
	{
		$name=$elements[1][$ie];

		//для получения блока текста
		$cdend = strpos($elements[3][$ie], "<");
		if ($cdend > 0)
			$xmlary[$name][$ie]["text"] = substr($elements[3][$ie], 0, $cdend);

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

?>
