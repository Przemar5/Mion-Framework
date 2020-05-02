<?php

declare(strict_types = 1);

namespace Core\Classes\Auth\Tokens;
use Core\Classes\Auth\Tokens\TokenAuth;
use Core\Classes\Auth\Tokens\UrlTokenAuth;
use Core\Classes\Session;
use App\Models\UserModel;
use App\Models\TokenModel;


class ActivateAccountTokenAuth extends TokenAuth
{
	public static function get($userId)
	{
		if (empty($userId) || !is_numeric($userId))
			return false;
		
		$token = self::generateUrlToken(80);
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
	
	public static function getUser($value)
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
}