<?php

namespace Core\Classes\Auth\Tokens;
use Core\Classes\Auth\Tokens\TokenAuth;
use Core\Classes\Session;
use App\Models\TokenModel;


class CsrfTokenAuth extends TokenAuth
{
	public static function get($length = 50, $secure = false)
	{
		$token = base64_encode(openssl_random_pseudo_bytes($length));
		
		if ($secure)
		{
			$data = [
				'name' => SESSION_CSRF_NAME,
				'value' => $token
			];
			$tokenModel->insert($data);
		}
		else
		{
			Session::set(SESSION_CSRF_NAME, $token);
		}
			
		return $token;
	}
	
	public static function check()
	{
		return Session::exists(SESSION_CSRF_NAME) && 
			Session::get(SESSION_CSRF_NAME) === $_POST['csrf'];
	}
	
	public static function delete()
	{
		Session::unset(SESSION_CSRF_NAME);
	}
}