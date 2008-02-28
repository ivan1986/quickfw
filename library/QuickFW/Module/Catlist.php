<?php

class QuickFW_Module_Catlist extends QuickFW_Module_Abstract
{
	public function getTemplate(&$smarty)
	{
		/* @var $smarty Smarty */
		
		global $db;
		
		if ($smarty->get_template_vars('install') == true) return '';
		$cats= @$db->select('SELECT
								id AS ARRAY_KEY,
								parent_id AS PARENT_KEY,
								name
							FROM ?_category'
						  );
//		print_r($cats);
		$smarty->assign('catlist_cats',$cats);
		return $smarty->fetch('modules/catlist.tpl');
	}
	
	public function getTimestamp(&$smarty)
	{
		return mktime();
	}

}

?>