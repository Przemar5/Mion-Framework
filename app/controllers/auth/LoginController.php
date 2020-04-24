<?php

namespace App\Controllers\Auth;
use Core\Classes\Auth\Tokens\CsrfTokenAuth;
use Core\Classes\Auth\Tokens\RememberMeTokenAuth;
use Core\Classes\Controller;
use Core\Classes\Cookie;
use Core\Classes\Form;
use Core\Classes\Hash;
use Core\Classes\Model;
use Core\Classes\Router;
use Core\Classes\Sanitizers\InputSanitizer;
use Core\Classes\Session;
use App\Models\UserModel;
use App\Models\TokenModel;


class LoginController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index_action()
	{
		$this->view->error = Session::pop('login_error');		
		$this->view->user = Form::getValues(['username', 'remember_me']);
		
		$this->view->render('auth/login/index');
	}
	
	public function verify_action()
	{
		if (isset($_POST) && !empty($_POST))
		{
			if (CsrfTokenAuth::check())
			{
				CsrfTokenAuth::delete();
				
				$this->loadModel('user');
				
				$username = InputSanitizer::sanitize($_POST['username']);
				$password = InputSanitizer::sanitize($_POST['password']);
				$rememberMe = (!empty($_POST['remember_me'])) ? 'checked' : '';

				if ($user = $this->userModel->findUser($username))
				{
					if (Hash::check($password, $user->password))
					{
						$user->setSession();
						
						if (!empty($rememberMe))
							$this->_rememberUser($user->id);
						
						if (strcasecmp($user->acl, 'Admin') === 0)
						{
							Router::redirect('dashboard');
							exit;
						}
						else
						{
							Router::redirect(DEFAULT_CONTROLLER);
							exit;
						}
					}
				}
				
				Form::saveValues(['username' => $username, 
								  'remember_me' => $rememberMe]);
				Session::set('login_error', 'Invalid login or password.');
			}
		}
		
		Router::redirect('login');
	}
	
	private function _rememberUser($userId)
	{
		$tokenModel = Model::load('token');
		$token = RememberMeTokenAuth::generate(50);
		$data = [
			'name' => COOKIE_REMEMBER_ME_NAME,
			'value' => $token,
			'user_id' => $userId
		];
		
		$tokenModel->insert($data);
		Cookie::set(COOKIE_REMEMBER_ME_NAME, $token, COOKIE_REMEMBER_ME_EXPIRY);
	}
}