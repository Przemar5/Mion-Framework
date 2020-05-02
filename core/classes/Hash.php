<?php

declare(strict_types = 1);

namespace Core\Classes;


class Hash
{
	public static function make($value): string
	{
		$options = [
			'cost' => 10
		];
		
		return password_hash($value, PASSWORD_ALGO, $options);
	}
	
	public static function check(string $value, string $hash): bool
	{
		return password_verify($value, $hash);
	}
}