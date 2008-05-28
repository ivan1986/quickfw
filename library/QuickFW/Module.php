<?php

class QuickFW_Module
{
	protected static $_thisInst = null;
	protected static $classes;
	
	protected function __construct()
	{
	}
	
	//TO DO: вынести переменную класса модуля, чтобы не создавать ее 2 раза
	//сохранять последний вызов IncludeFile - вызывыется 2 раза
	//вообще проверять его там и сразу юзать, если совпадает
	
	public static function getInstance()
	{
		if (self::$_thisInst === null)
		{
			self:: $_thisInst = new QuickFW_Module();
		}
		return self::$_thisInst;
	}
	
	public static function addStartControllerClass($name,&$class)
	{
		if (!isset(QuickFW_Module::$classes[$name]))
			QuickFW_Module::$classes[$name] = $class;
	}
	
	public static function getTemplate($tpl_name, &$tpl_source, &$smarty)
	{
		global $router,$params;
		$MCA=$router->moduleRoute($tpl_name);
		if (isset($MCA['Error']))
		{
			$tpl_source = "Ошибка подключения модуля ".$tpl_name." адрес был разобран в\t\t ".
				$MCA['Path']."\n".$MCA['Error'];
			return true;
		}
		
		if (!isset(QuickFW_Module::$classes[$MCA['Class']]))
			QuickFW_Module::$classes[$MCA['Class']] = new $MCA['Class']();
		$module = &QuickFW_Module::$classes[$MCA['Class']];
		
		list($lpPath, $router->ParentPath, $router->CurPath) = 
			array($router->ParentPath, $router->CurPath, $MCA['Path']);

		$result = call_user_func_array(array($module, $MCA['Action']), $MCA['Params']);
		
		list($router->CurPath, $router->ParentPath) = 
			array($router->ParentPath, $lpPath);
		
		if ($result === false)
			return true;
		
		$tpl_source = $result;
		return true;
	}
	
	function getTimestamp($tpl_name, &$tpl_timestamp, &$smarty)
	{
		global $router;
		$MCA=$router->moduleRoute($tpl_name);
		if (isset($MCA['Error'])) return true;
		
		if (!$MCA['ts'])
		{
			$tpl_timestamp = mktime();
			return true;
		}
			
		if (!isset(QuickFW_Module::$classes[$MCA['Class']]))
		{
			QuickFW_Module::$classes[$MCA['Class']] = new $MCA['Class']();
		}
		$module = &QuickFW_Module::$classes[$MCA['Class']];

		$result = call_user_func_array(array($module, 'getTimestamp'), array($MCAP['Action'],$MCAP['Params']));
		
		if ($result === false)
			return false;
		
		$tpl_timestamp = $result;
		return true;
	}

	function isSecure($tpl_name, &$smarty)
	{
		return true;
	}
	
	function isTrusted($tpl_name, &$smarty)
	{
		return false;
	}
}

?>