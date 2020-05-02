<?php

declare(strict_types = 1);

namespace Core\Classes;


class Session
{
	public static function start(?string $name = 'Cm9W4dIVrdlxnNmaeDSkha50', ?int $lifetime = 0, 
								 ?string $path = '/', ?string $domain = null, 
								 ?bool $secure = false, ?bool $httpOnly = true): void
	{
//		session_set_cookie_params($lifetime, $path, $domain ?? $_SERVER['HTTP_HOST'], $secure, $httpOnly);
		// To prevent finding session name by potential attacker
		session_name($name);
		session_cache_limiter('nocache');
		session_start();
		// To prevent session attack
		session_regenerate_id();
	}
	
	public static function set(string $name, $value): void
	{
		$_SESSION[$name] = $value;
	}
	
	public static function get(string $name)
	{
		return $_SESSION[$name];
	}
	
	public static function exists(string $name): bool
	{
		return isset($_SESSION[$name]);
	}
	
	public static function unset(string $name): void
	{
		unset($_SESSION[$name]);
	}
	
	public static function pop(string $name)
	{
		if (isset($_SESSION[$name]))
		{
			$value = $_SESSION[$name];
			unset($_SESSION[$name]);
			
			return $value;
		}
	}
	
	public static function regenerateId(): void
	{
		session_regenerate_id();
	}
	
	public static function end(): void
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