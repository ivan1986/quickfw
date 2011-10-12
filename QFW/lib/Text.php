<?php

define('_TS_MINUTE', 60);
define('_TS_HOUR',   _TS_MINUTE * 60);
define('_TS_DAY',    _TS_HOUR * 24);
define('_TS_WEEK',   _TS_DAY * 7);
define('_TS_MONTH',  _TS_DAY * 30);
define('_TS_YEAR',   _TS_DAY * 365);

/**
 * Хелперы для работы с текстовым выводом
 */
class Text
{

	/**
	 * Вывод окончаний русских слов с учетом числительных (например сообщение сообщения сообщений)
	 *
	 * @param intereg $n число
	 * @param string $form1 единственное
	 * @param string $form2 форма для 2-4
	 * @param string $form5 форма для 5 и более
	 * @return string нужная форма
	 */
	static public function pluralForm($n, $form1, $form2, $form5)
	{
		$n = abs($n) % 100;
		$n1 = $n % 10;
		if ($n > 10 && $n < 20) return $form5;
		if ($n1 > 1 && $n1 < 5) return $form2;
		if ($n1 == 1) return $form1;
		return $form5;
	}

	/**
	 * Обрезает длинные строки
	 *
	 * @param string $str Строка
	 * @param integer $size длина, до которой обрезать
	 * @param bool $word Обрезать по словам
	 * @return string Обрезанная строка
	 */
	static public function myTrim($str, $size, $word=false)
	{
		if (mb_strlen($str)<=$size)
			return $str;
		if (!$word)
			return mb_substr($str, 0, $size-3).'...';
		$str = str_replace("\r", "", $str);
		$str_space = str_replace("\n", " ", $str);
		$words = explode(' ', mb_substr($str_space, 0, $size-3));
		$len = mb_strlen(implode(' ', count($words) > 1 ? array_slice($words, 0, count($words)-1) : $words));
		$str = mb_substr($str, 0, $len).'...';
		return $str;
	}

	/**
	 * Печать размера файла в форматированном виде
	 */
	static public function printSize($size)
	{
		if ($size>1024*1024*2)
			return round($size/1024/1024,2).' Мб';
		if ($size>1024*10)
			return round($size/1024,2).' Кб';
		return $size.' байт';
	}

	/**
	 * Вывод сообщения с разбивкой длинных слов без повреждения тегов
	 *
	 * @param string $msg собщение, в котором нужно разбить слова
	 * @param integer $n длина, на которой ставить пробел
	 * @return string сообщение со словами, не превышающими n символов
	 */
	static public function msg2html($msg, $n=50)
	{
		$marker = " <> ";

		# Сохраняем все тэги чтобы уберечь их от разбивки
		preg_match_all("/(<.*?>)/si",$msg,$tags);

		# Заменяем все тэги на маркеры
		$msg =  preg_replace("/(<.*?>)/si", $marker, $msg);

		$msg = preg_replace('|\S{'.$n.'}|u','\0 ',$msg);

		# Восстанавливаем тэги в места которых были отмечены маркерами
		for ($i=0; $i<count($tags[0]);  $i++)
			$msg = preg_replace("/$marker/si", $tags[1][$i], $msg, 1);

		return $msg;
	}

	/**
	 * Преобразование URL в ссылки
	 */
	static public function make_urls($string)
	{
		$p = '/((?:(?:ht|f)tps?:\/\/|www\.)[^<\s\n]+)(?<![]\.,:;!\})<-])/msiu';
		$r = '<a href="$1">$1</a>$2';

		$string = preg_replace($p, $r, $string);

		$p = '/ href="www\./msiu';
		$r = ' href="http://www.';

		return preg_replace($p, $r, $string);
	}

	/**
	 * Генерирует урл из текста - убирает / и применяет urlencode
	 *
	 * @param string $text
	 * @return string
	 */
	static public function genUrl($text)
	{
	  return rawurlencode(str_replace(array('/', ' '), '-', $text));
	}

	/**
	 * Формирование даты по-русски
	 * <br>так как при выстановке локали неправильно склоняет
	 *
	 * @see date
	 * @param string $format формат аналогичен date
	 * @param integer $time время
	 * @return string дата
	 */
	static public function russian_date($format, $time=false)
	{
		static $translation = array(
			"January" => "Января",
			"February" => "Февраля",
			"March" => "Марта",
			"April" => "Апреля",
			"May" => "Мая",
			"June" => "Июня",
			"July" => "Июля",
			"August" => "Августа",
			"September" => "Сентября",
			"October" => "Октября",
			"November" => "Ноября",
			"December" => "Декабря",
			"Monday" => "Понедельник",
			"Tuesday" => "Вторник",
			"Wednesday" => "Среда",
			"Thursday" => "Четверг",
			"Friday" => "Пятница",
			"Saturday" => "Суббота",
			"Sunday" => "Воскресенье",
		);
		return strtr(date($format, $time!==false ? $time : time()), $translation);
	}

	/**
	 * Формирует из timestamp строку времяни
	 *
	 * @param integer $ts timestamp
	 * @return string строка - сколько времени назад
	 */
	static public function date_ago($ts) 
	{
		$dif = time() - $ts;

		$prefix = '';
		$special = false;

		if($dif < _TS_MINUTE) {
			$n = ($dif > 0 ? $dif : 1);

			if($n >= 25 and $n <= 35) {
				$word = 'полминуты';
				$special = true;
			}
			else {
				$word = self::pluralForm($n, 'секунду', 'секунды', 'секунд');
			}
		}
		elseif($dif < _TS_HOUR) {
			$n = (int) ($dif / _TS_MINUTE);

			if($n >= 25 and $n <= 35) {
				$word = 'полчаса';
				$special = true;
			}
			else {
				$word = self::pluralForm($n, 'минуту', 'минуты', 'минут');
			}
		}
		elseif($dif < _TS_DAY) {
			$n = (int) ($dif / _TS_HOUR);

			$n2 = (int) (($dif % _TS_HOUR) / _TS_MINUTE);

			if($n2 >= 20) {
				$prefix = 'более ';
				$word = self::pluralForm($n, 'часа', 'часов', 'часов');
			}
			else {
				$word = self::pluralForm($n, 'час', 'часа', 'часов');
			}
		}
		elseif($dif < _TS_WEEK) {
			$n = (int) ($dif / _TS_DAY);

			$n2 = (int) (($dif % _TS_DAY) / _TS_HOUR);

			if($n2 >= 6) {
				$prefix = 'более ';
				$word = self::pluralForm($n, 'дня', 'дней', 'дней');
			}
			else {
				$word = self::pluralForm($n, 'день', 'дня', 'дней');
			}
		}
		elseif($dif < _TS_MONTH) {
			$n = (int) ($dif / _TS_WEEK);

			$n2 = (int) (($dif % _TS_WEEK) / _TS_DAY);

			if($n2 >= 2) {
				$prefix = 'более ';
				$word = self::pluralForm($n, 'недели', 'недель', 'недель');
			}
			else {
				$word = self::pluralForm($n, 'неделю', 'недели', 'недель');
			}
		}
		elseif($dif < _TS_YEAR) {
			$n = (int) ($dif / _TS_MONTH);

			$n2 = (int) (($dif % _TS_MONTH) / _TS_WEEK);

			if($n2 >= 1) {
				$prefix = 'более ';
				$word = self::pluralForm($n, 'месяца', 'месяцев', 'месяцев');
			}
			else {
				$word = self::pluralForm($n, 'месяц', 'месяца', 'месяцев');
			}
		}
		else {
			$n = (int) ($dif / _TS_YEAR);

			$n2 = (int) (($dif % _TS_YEAR) / _TS_MONTH);

			if($n2 >= 1) {
				$prefix = 'более ';
				$word = self::pluralForm($n, 'года', 'лет', 'лет');
			}
			else {
				$word = self::pluralForm($n, 'год', 'года', 'лет');
			}
		}

		if($special) {
			$ret = $word;
		}
		elseif($n == 1) {
			$ret = "$prefix$word";
		}
		else {
			$ret = "$prefix$n $word";
		}

		return $ret;
	}


}