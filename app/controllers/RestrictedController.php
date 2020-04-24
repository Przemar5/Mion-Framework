<?php

namespace App\Controllers;
use Core\Classes\Controller;


class RestrictedController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index_action()
	{
		$this->view->render('restricted/index');
	}
}