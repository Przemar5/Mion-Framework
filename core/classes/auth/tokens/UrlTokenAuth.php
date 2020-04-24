<?php

namespace Core\Classes\Auth\Tokens;
use Core\Classes\Auth\Tokens\TokenAuth;


class UrlTokenAuth extends TokenAuth
{
	public static function generateUrlToken($length = 10)
	{
		return rtrim(strtr(base64_encode(openssl_random_pseudo_bytes($length)), '+/', '-_'), '=');
	}
}