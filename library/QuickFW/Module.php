<?php

require LIBPATH.'/QuickFW/Module/Abstract.php';

class QuickFW_Module
{
	protected static $_thisInst = null;
	
	protected function __construct()
	{
	}
	
	//TO DO: вынести переменную класса модуля, чтобы не создавать ее 2 раза
	//сохранять последний вызов IncludeFile - вызывыется 2 раза
	//вообще проверять его там и сразу юзать, если совпадает

	private static function IncludeFile($tpl_name)
	{
		global $router;
		$MCAP=$router->moduleRoute($tpl_name);
		if (!is_file($MCAP['File']))
		{
			$MCAP['error']=1;
			return $MCAP;
		}
		require_once $MCAP['File']; //пока once потом подумать как избавиться
		return $MCAP;
	}
	
	public static function getInstance()
	{
		if (self::$_thisInst === null)
		{
			self:: $_thisInst = new QuickFW_Module();
		}
		return self::$_thisInst;
	}
	
	public static function getTemplate($tpl_name, &$tpl_source, &$smarty)
	{
		$MCAP = self::IncludeFile($tpl_name);
		if (isset($MCAP['error']))
		{
			$tpl_source = "Ошибка подключения модуля ".$tpl_name." не найден файл<br />".$MCAP['File'];
			return true;
		}
		
		$classname=ucfirst($MCAP['Controller']).'Controller';
		if (class_exists($classname))
			$module = new $classname();
		else
		{
			$tpl_source = "Ошибка подключения модуля ".$tpl_name."\nв файле \t\t\t".$MCAP['File'].
						"\nне найден класс \t\t\t".$classname."\n";
			return true;
		}
			
		$aname=ucfirst($MCAP['Action']).'Module';
		if (!is_callable(array($module,$aname)))
		{
			$tpl_source = "Ошибка подключения модуля ".$tpl_name."\n в файле \t\t\t".$MCAP['File'].
				"\nнаходящемся в классе \t\t".$classname." \nне найдена функция \t\t".$aname."\n";
			return true;
		}
			
		$result = call_user_func_array(array($module, $aname), $MCAP['Params']);
		
		if ($result === false)
			return true;
		
		$tpl_source = $result;
		return true;
	}
	
	function getTimestamp($tpl_name, &$tpl_timestamp, &$smarty)
	{
		$MCAP = self::IncludeFile($tpl_name);
		if (isset($MCAP['error'])) return true;
		
		$classname=ucfirst($MCAP['Controller']).'Controller';
		if (class_exists($classname))
			$module = new $classname();
		else 
			return true;
			
		if (!is_callable(array($module,'getTimestamp')))
			return true;
			
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