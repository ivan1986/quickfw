<?php
/**
 * Поле с типом checkbox
 */
class Scaffold_Checkbox extends Scaffold_Field
{

	public function display($id, $value)
	{
		return $value ? 'Да' : 'Нет';
	}

	public function editor($id, $value)
	{
		return '<input type="hidden" name="'.$this->editName($id).'" value="0" />
			<input type="checkbox" name="'.$this->editName($id).'" value="1" label="'.$this->title.'"
				default="'.($value?'checked':'').'" />';
	}

	/**
	 * Часть условия WHERE для данного фильтра
	 *
	 * @param mixed $session сохраненное в сессии значение
	 * @return DbSimple_SubQuery часть запроса к базе данных
	 */
	public function filterWhere($session)
	{
		if ($session == 0)
			return QFW::$db->subquery('');
		return QFW::$db->subquery('?# LIKE ?',
			array($this->table=>$this->name), $session==1 ? '1' : '0');
	}

	/**
	 * Формирует поле ввода для фильтра
	 *
	 * @param mixed $session сохраненные в сесии данные
	 * @return string часть формы для фильтра
	 */
	public function filterForm($session)
	{
		return '<fieldset>'.$this->title.':
			<label><input type="radio" name="filter['.$this->name.']" value=""'.($session==0 ? ' checked': '').'> любой</label>
			<label><input type="radio" name="filter['.$this->name.']" value="1"'.($session==1 ? ' checked': '').'> установлен</label>
			<label><input type="radio" name="filter['.$this->name.']" value="-1"'.($session==-1 ? ' checked': '').'> сброшен</label>
		</fieldset>';
	}

}
