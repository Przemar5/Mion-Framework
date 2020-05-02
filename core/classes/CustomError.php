<?php

declare(strict_types = 1);

namespace Core\Classes;


class CustomError
{
	public static function log()
	{
		$error = debug_backtrace();
		dd($error);
	}
}