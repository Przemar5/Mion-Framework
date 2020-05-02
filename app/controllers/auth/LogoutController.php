<?php

declare(strict_types = 1);

namespace App\Controllers\Auth;
use Core\Classes\Auth\Tokens\RememberMeTokenAuth;
use Core\Classes\Controller;
use Core\Classes\Router;
use Core\Classes\Session;


class LogoutController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index_action(): void
	{
		$userId = Session::get(SESSION_USER_ID_NAME);
		RememberMeTokenAuth::forgetUser($userId);
		Session::end();
		
		Router::redirect('login');
	}
}