<?php

namespace Core\Classes\Auth;


class Auth
{
	public static function urlToken($length = 10)
	{
		return rtrim(strtr(base64_encode(openssl_random_pseudo_bytes($length)), '+/', '-_'), '=');
	}
	
	public static function token($length = 10)
	{
		return base64_encode(openssl_random_pseudo_bytes($length));
	}
}