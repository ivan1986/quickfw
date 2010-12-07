<?php
/**
 * Группа значений, от которых зависит показ поля
 *
 * @author ivan
 */
class Scaffold_Display
{
	public $list;
	public $edit;
	public $new;
	public $multiedit;
	public $multidel;

	public function __construct()
	{
		$this->edit = $this->list = $this->multiedit = $this->multidel = $this->new = true;
	}

	public function show()
	{

	}
	public function hide()
	{

	}

}

