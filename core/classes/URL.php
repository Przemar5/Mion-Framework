<?php

namespace Core\Classes;


class URL
{
	public static function split()
	{
		
	}
	
	public static function sanitize()
	{
		return (isset($_SERVER['PATH_INFO'])) ? 
			filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_URL) : '';
	}
	
	public static function currentUrl()
	{
		return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . 
			$_SERVER['REQUEST_URI'];
	}
}