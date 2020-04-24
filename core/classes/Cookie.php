<?php

namespace Core\Classes;


class Cookie
{
	public static function set($name, $value, $expiry = 3600, $secure = false)
	{
		setcookie($name, $value, time() + $expiry, '/', '', $secure, true);
	}
	
	public static function get($name)
	{
		return $_COOKIE[$name];
	}
	
	public static function exists($name)
	{
		return isset($_COOKIE[$name]);
	}
	
	public static function pop($name)
	{
		if (isset($_COOKIE[$name]))
		{
			$value = $_COOKIE[$name];
			setcookie($name, '', time() - 3600);
			
			return $value;
		}
	}
	
	public static function delete($name)
	{
		setcookie($name, '', time() - 3600, '/');
	}
}