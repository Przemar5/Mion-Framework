<?php

declare(strict_types = 1);

namespace App\Controllers\Auth;
use Core\Classes\Auth\Tokens\ResetPasswordTokenAuth;
use Core\Classes\Controller;
use Core\Classes\Router;
use App\Models\UserModel;


class VerificationController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function reset_password_action(): void
	{
		if (!isset($_GET['token']) || empty($_GET['token']))
		{
			Router::redirect(ACCESS_RESTRICTED);
			exit;
		}
		
		$user = ResetPasswordTokenAuth::getUser($_GET['token']);
		
		if (empty($user))
		{
			Router::redirect(ACCESS_RESTRICTED);
			exit;
		}
		
		$user->setSession();
		Router::redirect('reset-password');
		exit;
	}
	
	public function activate_account_action(): void
	{
		if (!isset($_GET['token']) || empty($_GET['token']))
		{
			Router::redirect(ACCESS_RESTRICTED);
			exit;
		}
		
		$user = Auth::activateAccountUser($_GET['token']);
		
		if (empty($user))
		{
			Router::redirect(ACCESS_RESTRICTED);
			exit;
		}
		
		$user->verified = 1;
		$user->save(['verified']);
		$user->setSession();
		
		if (strcasecmp($user->acl, 'admin'))
		{
			Router::redirect('dashboard');
			exit;
		}
		else
		{
			Router::redirect('home');
			exit;
		}
	}
}