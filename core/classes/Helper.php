<?php

declare(strict_types = 1);

namespace Core\Classes;


class Helper
{
	public static function escape(string $data): string
	{
		return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
	}
}