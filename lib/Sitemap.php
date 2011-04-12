<?php

/**
 * Класс для генерации sitemap
 */
class Sitemap
{
	/**
	 * Класс для генерации sitemap
	 *
	 * @param string $dir DOC_ROOT сайта
	 * @param string $prefix Префикс урла
	 * @param string $name Имя файла
	 * @param string $gzip Использовать gzip
	 */
	public function __construct($dir, $prefix, $name = 'sitemap', $gzip = false)
	{
		$this->dir = $dir;
		$this->curFileNum = 0;
		$this->prefix = $prefix;
		$this->name = $name;
		$this->gzip = $gzip;
		$this->head.="\n";
		$this->data = $this->head;
		$this->realsize = 10*1024*1024 - mb_strlen($this->head) - mb_strlen($this->foot);
		//$this->realsize = 400 - mb_strlen($this->head) - mb_strlen($this->foot);
		$this->last_len = $this->realsize;
		$this->last_urls = 50000;
	}

	/**
	 * Добавить путь
	 *
	 * @param string $url Урл
	 * @param array $extra Дополнительные поля
	 */
	public function add($url, $p, $extra = array())
	{
		if ($this->last_urls == 0)
		$this->nextFile();

		$text = '<url><loc>'.$this->prefix.$url.'</loc>';
		foreach($extra as $tag=>$value)
		$text.='<'.$tag.'>'.$value.'</'.$tag.'>';
		$text.='</url>'."\n";
		$len = mb_strlen($text);

		if ($this->last_len < $len)
			$this->nextFile();
		$this->last_len-=$len;

		$this->data.=$text;
	}

	/**
	 * Закончить генерацию
	 *
	 * @return bool Был ли создан индекс фаил?
	 */
	public function end()
	{
		//Файл был не один - генерим индекс
		if ($this->curFileNum)
		{
			//если у нас еще есть урлы для записи
			if ($this->last_urls != 50000)
				$this->nextFile();

			$index= '<?xml version="1.0" encoding="UTF-8"?>'."\n".
				'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
			for($i=1; $i<=$this->curFileNum; $i++)
			{
				$index.= "\t<sitemap>\n";
				$index.= "<loc>".$this->prefix.'/'.$this->name.$i.'.'.($this->gzip ? 'gz' : 'xml')."</loc>";
				$index.= "<lastmod>".(date(DATE_W3C))."</lastmod>";
				$index.= "</sitemap>";
			}
			$index.= '</sitemapindex>';
			file_put_contents($this->dir.'/'.$this->name.'_index.xml', $index);
		}
		else
			file_put_contents(($this->gzip ? 'compress.zlib://' : '').
				$this->dir.'/'.$this->name.'.'.($this->gzip ? 'gz' : 'xml'), $this->data.$this->foot);
		$index = (bool)$this->curFileNum;
		//восстанавливаем значения
		$this->data = $this->head;
		$this->last_len = $this->realsize;
		$this->last_urls = 50000;
		$this->curFileNum = 0;
		return $index;
	}

	/**
	 * Переключаемся на следующий файл
	 */
	private function nextFile()
	{
		$this->curFileNum++;
		file_put_contents(($this->gzip ? 'compress.zlib://' : '').
			$this->dir.'/'.$this->name.$this->curFileNum.'.'.($this->gzip ? 'gz' : 'xml'), $this->data.$this->foot);
		//восстанавливаем значения
		$this->data = $this->head;
		$this->last_len = $this->realsize;
		$this->last_urls = 50000;
	}

	/** @var integer Текущий номер файла */
	private $curFileNum;
	/** @var string Директория */
	private $dir;

	/** @var string Префикс урла */
	private $prefix;
	/** @var string Имя файла */
	private $name;
	/** @var bool использовать gzip */
	private $gzip;
	/** @var string генерируемый сайтмап */
	private $data;
	/** @var integer осталось адресов */
	private $last_urls;
	/** @var integer осталось байт */
	private $last_len;
	/** @var integer Размер без шапок */
	private $realsize;
	/** @var string шапка */
	private $head = '<?xml version="1.0" encoding="UTF-8"?><urlset'."\n".
	'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'."\n".
	'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n".
	'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
	/** @var string футер */
	private $foot = '</urlset>';

}
