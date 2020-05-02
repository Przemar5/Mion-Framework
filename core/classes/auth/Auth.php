<?php

namespace Core\Classes\Auth;


class Auth
{
	public static function urlToken(?int $length = 10): string
	{
		return rtrim(strtr(base64_encode(openssl_random_pseudo_bytes($length)), '+/', '-_'), '=');
	}
	
	public static function token(?int $length = 10): string
	{
		return base64_encode(openssl_random_pseudo_bytes($length));
	}
}