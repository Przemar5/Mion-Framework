<?php

declare(strict_types = 1);

namespace App\Controllers;
use Core\Classes\Controller;
use Core\Classes\Session;
use Core\Classes\Auth;


class HomeController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index_action(): void
	{
		$this->view->render('home/index');
	}
}