<?php

namespace Core\Classes;


class Session
{
	public static function start($name = 'Cm9W4dIVrdlxnNmaeDSkha50', $lifetime = 0, 
								 $path = '/', $domain = null, 
								 $secure = false, $httpOnly = true)
	{
//		session_set_cookie_params($lifetime, $path, $domain ?? $_SERVER['HTTP_HOST'], $secure, $httpOnly);
		// To prevent finding session name by potential attacker
		session_name($name);
		session_cache_limiter('nocache');
		session_start();
		// To prevent session attack
		session_regenerate_id();
	}
	
	public static function set($name, $value)
	{
		$_SESSION[$name] = $value;
	}
	
	public static function get($name)
	{
		return $_SESSION[$name];
	}
	
	public static function exists($name)
	{
		return isset($_SESSION[$name]);
	}
	
	public static function unset($name)
	{
		unset($_SESSION[$name]);
	}
	
	public static function pop($name)
	{
		if (isset($_SESSION[$name]))
		{
			$value = $_SESSION[$name];
			unset($_SESSION[$name]);
			
			return $value;
		}
	}
	
	public static function regenerateId()
	{
		session_regenerate_id();
	}
	
	public static function end()
	{
		$_SESSION = [];
		session_unset();
		
		if (ini_get('session.use_cookies'))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 3600,
					  $params['path'], $params['domain'],
					  $params['secure'], $params['httponly']);
		}
		session_destroy();
	}
}