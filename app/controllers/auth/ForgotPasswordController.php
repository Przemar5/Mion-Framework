<?php

namespace App\Controllers\Auth;
use Core\Classes\Auth\Tokens\CsrfTokenAuth;
use Core\Classes\Controller;
use Core\Classes\Emails\ForgotPasswordEmail;
use Core\Classes\Form;
use Core\Classes\Router;
use Core\Classes\Sanitizers\InputSanitizer;
use Core\Classes\Session;


class ForgotPasswordController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index_action()
	{
		$this->view->error = Session::pop('forgot_password_error');		
		$this->view->user = Form::getValues(['email']);
		
		$this->view->render('auth/forgot_password/index');
	}
	
	public function verify_action()
	{
		if (isset($_POST) && !empty($_POST))
		{
			if (CsrfTokenAuth::check())
			{
				CsrfTokenAuth::delete();
				$this->loadModel('user');
				
				$emailAddress = InputSanitizer::sanitize($_POST['email']);

				if (empty($emailAddress))
				{
					Session::set('forgot_password_error', 'Please enter your email address.');
					Router::redirect('forgot-password');
					exit;
				}
				else if ($user = $this->userModel->findEmail($emailAddress))
				{
					$token = Auth::resetPasswordToken($user->id);
					$email = new ForgotPasswordEmail;
					$data = [
						'receiver' => $emailAddress,
						'message' => [
							'token' => $token
						]
					];
					
					$email->prepare($data);
					
					if ($email->send())
					{
						Session::set('last_action', 'Check Your mail for reset password message.');
						Router::redirect(DEFAULT_CONTROLLER);
						exit;
					}
					else
					{
						Form::saveValues(['email' => $emailAddress]);
						Session::set('forgot_error', 'Could not send message. Please try again.');
						Router::redirect('forgot-password');
						exit;
					}	
				}
				
				Form::saveValues(['email' => $emailAddress]);
				Session::set('forgot_password_error', "Couldn't find any user with given email address.");
			}
		}
		
		Router::redirect('forgot-password');
	}
}