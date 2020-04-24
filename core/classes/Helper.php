<?php

namespace Core\Classes;


class Helper
{
	public static function escape($data)
	{
		return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
	}
}