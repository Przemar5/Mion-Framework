<?php

namespace App\Controllers\Auth;
use Core\Classes\Auth\Tokens\CsrfAuthToken;
use Core\Classes\Controller;
use Core\Classes\Router;
use Core\Classes\Sanitizers\InputSanitizer;
use Core\Classes\Session;
use App\Models\TokenModel;
use App\Models\UserModel;


class ResetPasswordController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index_action()
	{
		$this->view->error = Session::pop('reset_password_error');	
		$this->view->render('auth/reset_password/index');
	}
	
	public function verify_action()
	{
		if (isset($_POST) && !empty($_POST))
		{
			if (CsrfTokenAuth::check())
			{
				CsrfTokenAuth::delete();
				
				$user = UserModel::getUser(Session::get(SESSION_USER_ID_NAME));
				$user->password = InputSanitizer::sanitize($_POST['password']);
				$user->rePassword = InputSanitizer::sanitize($_POST['re_password']);
				
				if (!$user->changePassword())
				{
					Session::set('reset_password_error', $user->errors()['password']);
					Router::redirect('reset-password');
					exit;
				}
				else 
				{
					Session::set('last_action', 'Yuor password has been updated successfully.');
					Router::redirect('dashboard');
					exit;
				}
			}
		}
	}
}