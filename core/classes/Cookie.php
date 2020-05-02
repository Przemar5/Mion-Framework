<?php

declare(strict_types = 1);

namespace Core\Classes;


class Cookie
{
	public static function set(string $name, $value, ?int $expiry = 3600, ?bool $secure = false): void
	{
		setcookie($name, $value, time() + $expiry, '/', '', $secure, true);
	}
	
	public static function get(string $name)
	{
		return $_COOKIE[$name];
	}
	
	public static function exists(string $name): bool
	{
		return isset($_COOKIE[$name]);
	}
	
	public static function pop(string $name)
	{
		if (isset($_COOKIE[$name]))
		{
			$value = $_COOKIE[$name];
			setcookie($name, '', time() - 3600);
			
			return $value;
		}
	}
	
	public static function delete(string $name): void
	{
		setcookie($name, '', time() - 3600, '/');
	}
}