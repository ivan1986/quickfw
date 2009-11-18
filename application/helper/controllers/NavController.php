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
	public function pagerBlock($url='$', $all=0, $cur=1, $size=5, $name='pager')
	{
		if ($all<2) return '';
		QFW::$view->assign('pager',array(
			'all'=>$all,
			'c'=>$cur,
			'url'=>$url,
			'size'=>$size,
		));
		return QFW::$view->fetch($name.'.html');
	}

	/**
	 * Вывод меню списком с подсветкой текущего элемента
	 *
	 * @param array $items Массив элементов: ключ - заголовок, значение адресс
	 * @param string $cur текущий адресс
	 * @param string $id id для элемента ul - для оформления
	 * @param boolean $delDef Применять к ссылкам функцию QFW::$router->delDef
	 * @return string Сформированное меню
	 */
	public function menuBlock($items, $cur=false, $id='', $delDef = true)
	{
		if (count($items) == 0)
			return '';
		if ($cur == false)
			$cur = QFW::$router->RequestUri;
		$result = '<ul'.($id?' id="'.$id.'"':'').'>';
		foreach ($items as $k=>$v)
		{
			$result.='<li>';
			if (QFW::$router->delDef(trim($cur, '/')) == QFW::$router->delDef(trim($v, '/')))
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
