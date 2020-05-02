<?php

namespace Core\Classes\Auth\Tokens;
use Core\Classes\Auth\Tokens\TokenAuth;


class UrlTokenAuth extends TokenAuth
{
	public static function generateUrlToken(?int $length = 10): string
	{
		return rtrim(strtr(base64_encode(openssl_random_pseudo_bytes($length)), '+/', '-_'), '=');
	}
}