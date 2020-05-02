<?php

declare(strict_types = 1);

namespace Core\Classes\Auth\Tokens;
use Core\Classes\Auth\Tokens\TokenAuth;
use Core\Classes\Session;
use App\Models\UserModel;
use App\Models\TokenModel;


class ResetPasswordTokenAuth extends TokenAuth
{
	public static function get($userId)
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
	
	public static function check()
	{
//		return Session::exists(RESET_PASSWORD_TOKEN_NAME) && 
//			Session::get(RESET_PASSWORD_TOKEN_NAME) === ;
	}
	
	public static function getUser($value)
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
	
	public static function deleteResetPasswordToken()
	{
		Session::unset(RESET_PASSWORD_TOKEN_NAME);
	}
}