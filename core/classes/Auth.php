<?php

namespace Core\Classes;
use Core\Classes\Cookie;
use Core\Classes\Session;
use App\Models\TokenModel;
use App\Models\UserModel;


class Auth
{
	public static function csrfToken($length = 50)
	{
		$token = base64_encode(openssl_random_pseudo_bytes($length));
		
//		if ($secure)
//		{
//			$data = [
//				'name' => SESSION_CSRF_NAME,
//				'value' => $token
//			];
//			$tokenModel->insert($data);
//		}
		Session::set(SESSION_CSRF_NAME, $token);
		
		return $token;
	}
	
	public static function checkCsrfToken()
	{
		return Session::exists(SESSION_CSRF_NAME) && 
			Session::get(SESSION_CSRF_NAME) === $_POST['csrf'];
	}
	
	public static function deleteCsrfToken()
	{
		Session::unset(SESSION_CSRF_NAME);
	}
	
	public static function rememberUser($userId)
	{
		$tokenModel = Model::load('token');
		$token = self::token(50);
		$data = [
			'name' => COOKIE_REMEMBER_ME_NAME,
			'value' => $token,
			'user_id' => $userId
		];
		
		$tokenModel->insert($data);
		Cookie::set(COOKIE_REMEMBER_ME_NAME, $token, COOKIE_REMEMBER_ME_EXPIRY);
	}
	
	public static function isRememberedUser()
	{
		return Cookie::exists(COOKIE_REMEMBER_ME_NAME);
	}
	
	public static function forgetUser($userId)
	{
		$tokenModel = Model::load('token');
		$data = [
			'name' => COOKIE_REMEMBER_ME_NAME,
			'user_id' => $userId
		];
		
		$tokenModel->deleteWhere($data);
		Cookie::delete(COOKIE_REMEMBER_ME_NAME);
	}
	
	public static function getRememberedUser()
	{
		$token = Cookie::get(COOKIE_REMEMBER_ME_NAME);
		$tokenModel = Model::load('token');
		$userId = $tokenModel->findByToken(COOKIE_REMEMBER_ME_NAME, $token, false)->user_id;

		if (!$userId)
		{
			return;
		}
		
		$userModel = Model::load('user');
		$user = $userModel->findById($userId, false);
		
		if (!$user)
		{
			return;
		}
		
		Session::regenerateId();
		Session::set(SESSION_USER_ID_NAME, $userId);
		Session::set(SESSION_USER_ACL_NAME, $user->acl);
	}
	
	public static function resetPasswordToken($userId)
	{
		if (empty($userId) || !is_numeric($userId))
			return false;
		
		$token = self::urlToken(80);
		$tokenModel = Model::load('token');
		$data = [
			'name' => RESET_PASSWORD_TOKEN_NAME,
			'user_id' => $userId
		];
		$tokenModel->deleteWhere($data);
		
		$data = [
			'name' => RESET_PASSWORD_TOKEN_NAME,
			'value' => $token,
			'user_id' => $userId,
			'expiry' => date('Y-m-d H:i:s', time() + RESET_PASSWORD_TOKEN_EXPIRY)
		];
		
		$tokenModel->insert($data);
		
		return $token;
	}
	
	public static function activateAccountToken($userId)
	{
		if (empty($userId) || !is_numeric($userId))
			return false;
		
		$token = self::urlToken(80);
		$tokenModel = Model::load('token');
		$data = [
			'name' => ACTIVATE_ACCOUNT_TOKEN_NAME,
			'user_id' => $userId
		];
		$tokenModel->deleteWhere($data);
		
		$data = [
			'name' => ACTIVATE_ACCOUNT_TOKEN_NAME,
			'value' => $token,
			'user_id' => $userId
		];
		
		$tokenModel->insert($data);
		
		return $token;
	}
	
	public static function checkResetPasswordToken()
	{
//		return Session::exists(RESET_PASSWORD_TOKEN_NAME) && 
//			Session::get(RESET_PASSWORD_TOKEN_NAME) === ;
	}
	
	public static function resetPasswordUser($value)
	{
		if (!preg_match('/^[0-9a-zA-Z_\-\+]+$/', $value))
			return false;
		
		$tokenModel = Model::load('token');
		$data = [
			'bind' => [RESET_PASSWORD_TOKEN_NAME, $value],
			'conditions' => 'name = ? AND value = ?'
		];

		if (!$token = $tokenModel->findFirst($data))
			return false;
		
		if (time() > strtotime($token->expiry))
			return false;
		
		if (!$user = UserModel::getUser($token->user_id))
			return false;
		
		$token->delete();
		
		return $user;
	}
	
	public static function activateAccountUser($value)
	{
		if (!preg_match('/^[0-9a-zA-Z_\-\+]+$/', $value))
			return false;
		
		$tokenModel = Model::load('token');
		$data = [
			'bind' => [ACTIVATE_ACCOUNT_TOKEN_NAME, $value],
			'conditions' => 'name = ? AND value = ?'
		];

		if (!$token = $tokenModel->findFirst($data))
			return false;
		
		if (!$user = UserModel::getUser($token->user_id))
			return false;
		
		$token->delete();
		
		return $user;
	}
	
	public static function deleteResetPasswordToken()
	{
		Session::unset(RESET_PASSWORD_TOKEN_NAME);
	}
	
	public static function urlToken($length = 10)
	{
		return rtrim(strtr(base64_encode(openssl_random_pseudo_bytes($length)), '+/', '-_'), '=');
	}
	
	public static function token($length = 10)
	{
		return base64_encode(openssl_random_pseudo_bytes($length));
	}
}