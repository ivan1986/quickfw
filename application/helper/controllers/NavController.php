<?php

class NavController
{
	/**
	 * Пагинатор
	 *
	 * <br>готовый пейджинатор с несколькими шаблонами
	 * которому указывается урл и страницы
	 *
	 * @param string $url Шаблон урла, номер страницы заменен $
	 * @param integer $all Всего страниц
	 * @param integer $cur Текущая страница
	 * @param integer $size По сколько страниц отображать
	 * @param string $name Имя шаблона пагинатора
	 * @return string Сформированный пагинатор
	 */
	public function pagerBlock($url='$', $all=0, $cur=1, $name='pager')
	{
		if ($all<2) return '';
		QFW::$view->assign('pager',array(
			'all'=>$all,
			'c'=>$cur,
			'url'=>$url,
		));
		return QFW::$view->fetch('pager/'.$name.'.php');
	}

	/**
	 * Пагинатор
	 *
	 * <br>готовый пейджинатор с несколькими шаблонами
	 * которому указывается урл и с какого элемента считать и сколько элементов
	 *
	 * @param string $url Шаблон урла, номер страницы заменен $
	 * @param int $from с какого элемента
	 * @param int $items сколько элементов
	 * @param int $size По сколько отображать на странице
	 * @param string $name Имя шаблона пагинатора
	 * @return string
	 */
	public function pagerFromBlock($url='$', $from, $items=0, $size = 10, $name='pager')
	{
		$all = ceil($items / $size);
		$cur = ceil($from / $size);
		return $this->pagerBlock($url, $all, $cur, $name);
	}


	/**
	 * Древоводное меню на вложенными списками
	 *
	 * @param array $items Массив элементов:
	 * <br>каждый элемент - array(
	 * <br> 'title' => Заголовок,
	 * <br> 'url' => адрес(Url|строка|false),
	 * <br> ['show'] => true|false показывать или нет
	 * <br> 'childNodes' => вложенный массив аналогичной структуры,
	 * <br>)
	 * <br>остальные элементы игнорируются
	 * @param string $id id для элемента ul - для оформления
	 * @param Url $cur текущий адресс
	 * @return string Сформированное меню
	 */
	public function menuTreeBlock($items, $id='', $cur=false)
	{
		if (count($items) == 0)
			return '';
		if ($cur == false)
			$cur = QFW::$router->RequestUri;
		if ($cur instanceof Url)
			$cur = $cur->intern();
		$result = '<ul'.($id?' id="'.$id.'"':'').'>';
		$result.=$this->menuTreeNodes($items, $cur);
		$result.= '</ul>';
		return $result;
	}

	/**
	 * Рекурсивная функция формирования меню
	 *
	 * @param array $items Массив элементов - аналогичен menuTreeBlock
	 * @param Url $cur текущий адресс
	 * @return string Сформированное подменю
	 */
	private function menuTreeNodes($items, $cur)
	{
		$result = '';
		foreach ($items as $v)
		{
			if (isset($v['show']) && $v['show'] === false)
				continue;
			$self = $cur == ($v['url'] instanceof Url ? $v['url']->intern() : $v['url']);
			$result.='<li'.($self?' class="cur"':'').'>';
			if ($v['url'] === false)
				$result.=$v['title'];
			else
				$result.= $self ? '<b>'.$v['title'].'</b>' : '<a href="'.$v['url'].'">'.$v['title'].'</a>';
			if (isset($v['childNodes']))
				$result.="\n<ul>".$this->menuTreeNodes($v['childNodes'], $cur).'</ul>';
			$result.="</li>\n";
		}
		return $result;
	}

	/**
	 * Вывод меню списком с подсветкой текущего элемента
	 *
	 * @param array $items Массив элементов:
	 * <br>ключ - заголовок, значение Url|false
	 * @param string $id id для элемента ul - для оформления
	 * @param Url $cur текущий адресс
	 * @return string Сформированное меню
	 */
	public function menuBlock($items, $id='', $cur=false)
	{
		if (count($items) == 0)
			return '';
		if ($cur == false)
			$cur = QFW::$router->RequestUri;
		if ($cur instanceof Url)
			$cur = $cur->intern();
		$result = '<ul'.($id?' id="'.$id.'"':'').'>';
		foreach ($items as $k=>$v)
		{
			$result.='<li>';
			if ($v === false)
				$result.=$k;
			else
			{
				if ($v instanceof Url)
					$self = $cur == $v->intern();
				else
					$self = $cur == $v;
				$result.= $self ? '<b>'.$k.'</b>' : '<a href="'.$v.'">'.$k.'</a>';
			}
			$result.="</li>\n";
		}
		$result.= '</ul>';
		return $result;
	}

	/**
	 * Вывод меню списком с подсветкой текущего элемента
	 *
	 * @param array $items Массив элементов:
	 * <br>ключ - заголовок, значение адресс|false
	 * @param string $id id для элемента ul - для оформления
	 * @param string $cur текущий адресс
	 * @param boolean $delDef Применять к ссылкам функцию QFW::$router->delDef
	 * @return string Сформированное меню
	 */
	public function menuOldBlock($items, $id='', $cur=false, $delDef = true)
	{
		if (count($items) == 0)
			return '';
		if ($cur == false)
			$cur = QFW::$router->RequestUri;
		$result = '<ul'.($id?' id="'.$id.'"':'').'>';
		foreach ($items as $k=>$v)
		{
			$result.='<li>';
			if ($v === false)
				$result.=$k;
			elseif (QFW::$router->delDef(trim($cur, '/')) == QFW::$router->delDef(trim($v, '/')))
				$result.='<b>'.$k.'</b>';
			else
			{
				if ($delDef)
					$v = QFW::$router->delDef($v);
				$result.='<a href="'.QFW::$view->P->siteUrl($v).'">'.$k.'</a>';
			}
			$result.="</li>\n";
		}
		$result.= '</ul>';
		return $result;
	}

}

?>
