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
?>
