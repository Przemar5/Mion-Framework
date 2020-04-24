<?php

namespace App\Controllers\Auth;
use Core\Classes\Auth\Tokens\ActivateAccountTokenAuth;
use Core\Classes\Auth\Tokens\CsrfTokenAuth;
use Core\Classes\Controller;
use Core\Classes\Emails\ActivateAccountEmail;
use Core\Classes\Form;
use Core\Classes\Router;
use Core\Classes\Session;
use Core\Classes\Sanitizers\InputSanitizer;
use App\Models\UserModel;
use App\Models\TestModel;


class RegisterController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index_action()
	{
		$this->view->errors = Session::pop('register_errors');
		$this->view->user = Form::getValues(['username', 'email', 
											 'first_name', 'last_name']);
		$this->view->render('auth/register/index');
	}
	
	public function verify_action()
	{
		if (isset($_POST) && !empty($_POST))
		{
			if (CsrfTokenAuth::check())
			{
				CsrfTokenAuth::delete();
				
				$user = new UserModel;
				
				$user->username = InputSanitizer::sanitize($_POST['username']);
				$user->email = InputSanitizer::sanitize($_POST['email']);
				$user->first_name = InputSanitizer::sanitize($_POST['first_name']);
				$user->last_name = InputSanitizer::sanitize($_POST['last_name']);
				$user->password = InputSanitizer::sanitize($_POST['password']);
				$user->rePassword = InputSanitizer::sanitize($_POST['re_password']);
				
				if ($user->register())
				{
					$token = ActivateAccountTokenAuth::get($user->id);
					$email = new ActivateAccountEmail;
					$data = [
						'receiver' => $user->email,
						'message' => [
							'token' => $token
						]
					];
					
					$email->prepare($data);
					
					if ($email->send())
					{
						Session::set('last_action', 'Check your email for account activation email.');
						Router::redirect('home');
						exit;
					}
					else 
					{
						$user->delete();
						Form::saveValues(['username' => $user->username, 
									 'email' => $user->email,
									 'first_name' => $user->first_name,
									 'last_name' => $user->last_name]);
						Session::set('registration_errors', 'Something gone wrong. Please fill fields again.');
						
						Router::redirect('register');
						exit;
					}
				}
				
				Form::saveValues(['username' => $user->username, 
								 'email' => $user->email,
								 'first_name' => $user->first_name,
								 'last_name' => $user->last_name]);
				Session::set('register_errors', $user->getErrorMessages());
			}
		}
		
		Router::redirect('register');
		exit;
	}
}