<?php

namespace Core\Classes\Auth\Tokens;


class TokenAuth
{
	public static function generate($length = 10)
	{
		return base64_encode(openssl_random_pseudo_bytes($length));
	}
}