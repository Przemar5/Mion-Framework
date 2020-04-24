<?php

namespace Core\Classes;


class CustomError
{
	public static function log()
	{
		$error = debug_backtrace();
		dd($error);
	}
}