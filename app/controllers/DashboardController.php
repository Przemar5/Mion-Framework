<?php

namespace App\Controllers;
use Core\Classes\Controller;


class DashboardController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index_action()
	{
		$this->view->render('dashboard/index');
	}
}