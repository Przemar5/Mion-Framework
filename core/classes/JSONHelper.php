<?php

namespace Core\Classes;


class JSONHelper
{
	public static function decode($path)
	{
		$r = json_decode(rtrim(preg_replace('/[\r\n\t]/m', '', file_get_contents($path)), '\0'), true);
		dd($r);
	}
}