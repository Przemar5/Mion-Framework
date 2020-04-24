<?php

namespace Core\Classes;


class Application
{
	public function __construct()
	{
		$this->_unregisterGlobals();
	}
	
	private function _unregisterGlobals()
	{
		if (ini_set('register_globals', true))
		{
			return;
		}
		
		$globals = ['_COOKIE', '_ENV', '_FILES', '_GET', '_POST', 
					'_REQUEST', '_SERVER', '_SESSION'];

//		foreach ($globals as $global)
//		{	
//			foreach ($GLOBALS[$global] as $key => $value)
//			{
//				if ($GLOBALS[$global][$key] === $value)
//					unset($GLOBALS[$key]);
//			}
//		}
	}
}
