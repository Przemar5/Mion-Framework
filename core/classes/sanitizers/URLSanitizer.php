<?php

namespace Core\Classes\Sanitizers;
use Core\Classes\Sanitizers\CustomSanitizer;


class URLSanitizer extends CustomSanitizer
{
	public static function sanitize($value)
	{
		return strtr(filter_var($value, FILTER_SANITIZE_URL), '-', '');
	}
}