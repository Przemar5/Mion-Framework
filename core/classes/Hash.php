<?php

namespace Core\Classes;


class Hash
{
	public static function make($value)
	{
		$options = [
			'cost' => 10
		];
		
		return password_hash($value, PASSWORD_ALGO, $options);
	}
	
	public static function check($value, $hash)
	{
		return password_verify($value, $hash);
	}
}