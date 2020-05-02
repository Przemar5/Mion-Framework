<?php

namespace Core\Classes\Auth\Tokens;


class TokenAuth
{
	public static function generate(?int $length = 10): string
	{
		return base64_encode(openssl_random_pseudo_bytes($length));
	}
}