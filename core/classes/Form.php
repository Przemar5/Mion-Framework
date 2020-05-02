<?php

declare(strict_types = 1);

namespace Core\Classes;
use Core\Classes\Cookie;
use Core\Classes\Helper;
use Core\Classes\HTML;
use Core\Classes\Auth;


class Form extends HTML
{
	public static function form()
	{
		
	}
	
	public static function saveValues(array $fields, ?bool $secure = false): void
	{
		$prefix = 'form_';
		
		if (empty($fields) || !is_array($fields))
		{
			return;
		}
		
		foreach ($fields as $field => $value)
		{
			if ($secure)
				Session::set($prefix . $field, $value);
			else
				Cookie::set($prefix . $field, $value, 60);
		}
		
		if ($secure)
			Cookie::set($prefix . 'security', 1);
	}
	
	public static function getValues(array $fields)
	{
		$prefix = 'form_';
		$values = (object) [];
		$secure = Cookie::pop($prefix . 'security');
		
		if (empty($fields) || !is_array($fields))
		{
			return;
		}
		
		foreach ($fields as $field)
		{
			$values->$field = ($secure) 
				? Session::pop($prefix . $field) 
				: Cookie::pop($prefix . $field);
		}
		
		return $values;
	}
	
	public static function csrf(): string
	{
		$name = SESSION_CSRF_NAME;
		$token = Auth::csrfToken();
		
		return '<input type="hidden" name="csrf" value="' . $token . '"/>';
	}
	
	public static function token(string $name): string
	{
		if ($name = 'reset_password')
		{
			$token = Auth::resetPasswordToken();
		}
		
		return '<input type="hidden" name="' . $name . '" value="' . $token . '"/>';
	}
}