<?php
/**
 * Класс со статическими функциями-хелперами
 *
 * @author ivan
 */
class Helpers
{
	static protected $Head = array();
	static protected $HeadData = array();

	static protected $IncFiles = array(
		'js_main'=>array(),
		'js'=>array(),
		'css_main'=>array(),
		'css'=>array(),
	);
	/** @var bool флаг, что начался вывод в mainTemplate */
	static protected $isMain = false;

	/**
	 * Установка флага вывода main
	 * @internal Не вызывать, должна быть открыта из-за отсутствия friends
	 */
	static public function startDisplayMain() { self::$isMain = true; }

	/**
	 * Добавление js файла
	 *
	 * @param string $file путь к файлу
	 * @param bool $noBase не добавлять baseurl
	 * @return true
	 */
	static public function addJS($file, $noBase=false)
	{
		self::$IncFiles['js'.(self::$isMain?'_main':'')][]=($noBase?'':QFW::$config['redirection']['baseUrl']).$file;
		return true;
	}

	/**
	 * Добавление css файла
	 *
	 * @param string $file путь к файлу
	 * @param bool $noBase не добавлять baseurl
	 * @return true
	 */
	static public function addCSS($file, $noBase=false)
	{
		self::$IncFiles['css'.(self::$isMain?'_main':'')][]=($noBase?'':QFW::$config['redirection']['baseUrl']).$file;
		return true;
	}

	//Cтандартные вставки - JS в начало и в конец документа и CSS в начало
	static public function JSh($data) {return self::getHead($data,'_JavaScript2HEAD',true);}
	static public function JSe($data) {return self::getHead($data,'_JavaScript2END',true);}
	static public function CSS($data) {return self::getHead($data,'_CSS2HEAD',true);}
	static public function sJSh() {return self::getHead(false,'_JavaScript2HEAD',true);}
	static public function eJSh() {return self::getHead(true ,'_JavaScript2HEAD',true);}
	static public function sJSe() {return self::getHead(false,'_JavaScript2END',true);}
	static public function eJSe() {return self::getHead(true ,'_JavaScript2END',true);}
	static public function sCSS() {return self::getHead(false,'_CSS2HEAD',true);}
	static public function eCSS() {return self::getHead(true ,'_CSS2HEAD',true);}

	//сокращения для JavaScript
	static public function sJS($name='') {return self::getHead(false,'_JavaScript_'.$name,true);}
	static public function eJS($name='') {return self::getHead(true ,'_JavaScript_'.$name,true);}
	static public function oJS($name='') {return self::outHead('_JavaScript_'.$name,"<script type=\"text/javascript\"><!--\n","\n--></script>\n");}

	static public function outHead($name='default', $pre='',$post='')
	{
		$key = '<!--HEAD'.$name.'-->';
		if (array_key_exists($key,self::$Head))
			return '';
		self::$Head[$key]=array(
			'pre'=>$pre,
			'post'=>$post,
		);
		return $key;
	}

	static public function getHead($content, $name='default', $join=false)
	{
		//для перехвата из PlainPHP
		if ($content===false)
		{
			ob_start();
			return;
		}
		if ($content===true)
		{
			$content=ob_get_contents();
			ob_end_clean();
		}

		$k = '<!--HEAD'.$name.'-->';

		if (!isset(self::$HeadData[$k]))
			self::$HeadData[$k]='';
		if ($join)
			self::$HeadData[$k].=$content;
		else
			self::$HeadData[$k]=$content;
	}

	/**
	 * Обработка результата после вывода
	 *
	 * @internal из-за отсутствия friends
	 * @param string $text
	 * @return string
	 */
	static public function HeaderFilter($text)
	{
		$head='';
		$endSlash = QFW::$config['QFW']['addCSSXml'] ? '/' : '';
		self::$IncFiles['css'] = array_merge(self::$IncFiles['css_main'], self::$IncFiles['css']);
		self::$IncFiles['js'] = array_merge(self::$IncFiles['js_main'], self::$IncFiles['js']);

		self::$IncFiles['css'] = array_unique(self::$IncFiles['css']);
		if (count(self::$IncFiles['css'])>0)
			$head.='<link rel="stylesheet" href="'.
				join('" type="text/css" '.$endSlash.'>'."\n".'<link rel="stylesheet" href="', self::$IncFiles['css']).
				'" type="text/css" '.$endSlash.'>'."\n";

		self::$IncFiles['js'] = array_unique(self::$IncFiles['js']);
		if (count(self::$IncFiles['js'])>0)
			$head.='<script src="'.
				join('" type="text/javascript"></script>'."\n".'<script src="', self::$IncFiles['js']).
					'" type="text/javascript"></script>'."\n";

		foreach (self::$HeadData as $k=>$v)
		{
			if ($k=='<!--HEAD_JavaScript2HEAD-->')
				$head.="<script type=\"text/javascript\"><!--\n".$v."\n--></script>\n";
			elseif ($k=='<!--HEAD_JavaScript2END-->')
				$text = str_replace('</body>',"<script type=\"text/javascript\"><!--\n".$v."\n--></script>\n</body>",$text);
			elseif ($k=='<!--HEAD_CSS2HEAD-->')
				$head.="<style type=\"text/css\">\n".$v."\n</style>\n";
			elseif (!array_key_exists($k,self::$Head)) //если нету ключа, то добавляем вверх
				$head.=$v;
			elseif ($v!='') //если есть, то обрамляем pre и post и вставляем
			{
				self::$HeadData[$k]=self::$Head[$k]['pre'].$v.self::$Head[$k]['post'];
				continue; //оставляем элемент в массиве
			}
			unset(self::$HeadData[$k]);
		}
		$head.="</head>\n";

		$text = str_replace('</head>',$head,$text);
		$text = str_replace(array_keys(self::$HeadData),array_values(self::$HeadData),$text);
		$text = preg_replace('|<!--HEAD.*?-->|','',$text);
		return $text;
	}

	/**
	 * Отображение сообщений об ошибках
	 */
	static public function displayErrors($errors=array())
	{
		$res = '';
		if (!is_array($errors))
			return $res;
		foreach($errors as $error)
			$res .= self::$displayErrorsParams['pre'].$error.self::$displayErrorsParams['post'];
		return $res;
	}

	static protected $displayErrorsParams = array('pre'=>'', 'post'=>'');

	/**
	 * Установка обрамления сообщений об ошибках
	 */
	static public function setDisplayErrorsParams($pre='', $post='')
	{
		self::$displayErrorsParams = array('pre'=>$pre, 'post'=>$post);
	}

}
?>
