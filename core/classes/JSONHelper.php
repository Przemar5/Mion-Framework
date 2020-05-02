<?php

declare(strict_types = 1);

namespace Core\Classes;


class JSONHelper
{
	public static function decode(string $path)
	{
		$r = json_decode(rtrim(preg_replace('/[\r\n\t]/m', '', file_get_contents($path)), '\0'), true);
		dd($r);
	}
}