<?php

class QuickFW_Block
{
	protected static $_thisInst = null;

	protected function __construct()
	{
	}

	public static function getInstance()
	{
		if (self::$_thisInst === null)
			self::$_thisInst = new QuickFW_Block();
		return self::$_thisInst;
	}

	public static function getTemplate($tpl_name)
	{
		global $router;
		$MCA=$router->blockRoute($tpl_name);
		if (isset($MCA['Error']))
			return "Ошибка подключения блока ".$tpl_name." адрес был разобран в\t\t ".
				$MCA['Path']."\n".$MCA['Error'];

		$CacheInfo=false;
		if ($MCA['cache'])
		{
			$CacheInfo=$MCA['Class']->CacheInfo($MCA['Action'],$MCA['Params']);
			if (array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
			$data = $CacheInfo['Cacher']->load($CacheInfo['id']);
			if ($data)
				return $data;
		}

		list($lpPath, $router->ParentPath, $router->CurPath) =
			array($router->ParentPath, $router->CurPath, $MCA['Path']);

		$result = call_user_func_array(array($MCA['Class'], $MCA['Action']), $MCA['Params']);

		list($router->CurPath, $router->ParentPath) =
			array($router->ParentPath, $lpPath);

		if ($CacheInfo)
		{
			if (array_key_exists('Cacher',$CacheInfo) && array_key_exists('id',$CacheInfo))
			{
				if (array_key_exists('time',$CacheInfo))
					$CacheInfo['Cacher']->save($result,$CacheInfo['id'],
						array_key_exists('tags',$CacheInfo)?$CacheInfo['tags']:array(),
						$CacheInfo['time']
					);
				else
					$CacheInfo['Cacher']->save($result,$CacheInfo['id'],
						array_key_exists('tags',$CacheInfo)?$CacheInfo['tags']:array()
					);
			}
		}

		return $result;
	}

}

?>