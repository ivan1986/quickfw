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
   * @param string $suffix Суффикс файла
   */
  public function __construct($dir, $prefix, $suffix='')
  {
    $this->dir = $dir;
    $this->curFileNum = 0;
    $this->prefix = $prefix;
    $this->suffix = $suffix;
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
   * @param float $priority Приоритет
   * @param array $extra Дополнительные поля
   */
  public function add($url, $priority, $extra = array())
  {
    if ($this->last_urls == 0)
      $this->nextFile();

    $text = '<url><loc>'.$this->prefix.$url.'</loc>'.
        '<priority>'.$priority.'</priority>';
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
   */
  public function end()
  {
    //Файл был не один - генерим индекс
    if ($this->curFileNum)
    {
      $this->curFileNum++;
      file_put_contents($this->dir.'/sitemap'.$this->suffix.$this->curFileNum.'.xml', $this->data.$this->foot);

      $index= '<?xml version="1.0" encoding="UTF-8"?>'."\n".
'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
      for($i=1; $i<=$this->curFileNum; $i++)
      {
        $index.= "\t<sitemap>\n";
        $index.= "<loc>".$this->prefix.'/sitemap'.$this->suffix.$i.'.xml'."</loc>";
        $index.= "<lastmod>".(date(DATE_W3C))."</lastmod>";
        $index.= "</sitemap>";
      }
      $index.= '</sitemapindex>';
      file_put_contents($this->dir.'/sitemap'.$this->suffix.'_index.xml', $index);
    }
    else
      file_put_contents($this->dir.'/sitemap'.$this->suffix.'.xml', $this->data.$this->foot);
    //восстанавливаем значения
    $this->data = $this->head;
    $this->last_len = $this->realsize;
    $this->last_urls = 50000;
    $this->curFileNum = 0;
  }

  /**
   * Переключаемся на следующий файл
   */
  private function nextFile()
  {
    $this->curFileNum++;
    file_put_contents($this->dir.'/sitemap'.$this->suffix.$this->curFileNum.'.xml', $this->data.$this->foot);
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
  /** @var string Суффикс файла */
  private $suffix;
  /** @var string генерируемый сайтмап */
  private $data;
  /** @var integer осталось адресов */
  private $last_urls;
  /** @var integer осталось байт */
  private $last_len;
  /** @var integer Размер без шапок */
  private $realsize;
  /** @var string шапка */
  private $head = '<?xml version="1.0" encoding="UTF-8"?><urlset
  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                      http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
  /** @var string футер */
  private $foot = '</urlset>';


}
