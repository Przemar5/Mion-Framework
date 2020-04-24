<?php

namespace Core\Classes\Auth\Tokens;
use Core\Classes\Auth\Tokens\TokenAuth;
use Core\Classes\Cookie;
use Core\Classes\Model;
use Core\Classes\Session;
use App\Models\TokenModel;


class RememberMeTokenAuth extends TokenAuth
{
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
}