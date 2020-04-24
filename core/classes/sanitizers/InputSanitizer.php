<?php

namespace Core\Classes\Sanitizers;
use Core\Classes\Sanitizers\CustomSanitizer;


class InputSanitizer extends CustomSanitizer
{
	public static function sanitize($value)
	{
		return strip_tags(htmlspecialchars(trim($value)));
	}
}