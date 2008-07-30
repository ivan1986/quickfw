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
		global $router;
		$MCA=$router->moduleRoute($tpl_name);
		if (isset($MCA['Error']))
		{
			$tpl_source = "Ошибка подключения модуля ".$tpl_name." адрес был разобран в\t\t ".
				$MCA['Path']."\n".$MCA['Error'];
			return true;
		}
		
		if (!isset(QuickFW_Module::$classes[$MCA['Class']]))
		{
			QuickFW_Module::$classes[$MCA['Class']] = new $MCA['Class']();
		}
		$module = &QuickFW_Module::$classes[$MCA['Class']];
		
		$CacheInfo=false;
		if ($MCA['cache'])
		{
			$CacheInfo=$module->CacheInfo($MCA['Action'],$MCA['Params']);
			if (array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
			$data = $CacheInfo['Cacher']->load($CacheInfo['id']);
			if ($data)
			{
				$tpl_source = $data;
				return true;
			}
		}
		
		list($lpPath, $router->ParentPath, $router->CurPath) = 
			array($router->ParentPath, $router->CurPath, $MCA['Path']);

		$result = call_user_func_array(array($module, $MCA['Action']), $MCA['Params']);
		
		list($router->CurPath, $router->ParentPath) = 
			array($router->ParentPath, $lpPath);
		
		if ($result === false)
			return true;
		
		$tpl_source = $result;
		
		if ($CacheInfo)
		{
			if (array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
			{
		 		if (array_key_exists('time',$CacheInfo))
				 	$CacheInfo['Cacher']->save($tpl_source,$CacheInfo['id'],
				 		array_key_exists('tags',$CacheInfo)?$CacheInfo['tags']:array(),
				 		$CacheInfo['time']
			 		);
			 	else 
				 	$CacheInfo['Cacher']->save($tpl_source,$CacheInfo['id'],
				 		array_key_exists('tags',$CacheInfo)?$CacheInfo['tags']:array()
			 		);
			}
		}
		
		return true;
	}
	
}

?>