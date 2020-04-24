<?php

namespace App\Controllers;
use Core\Classes\Controller;


class ErrorController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function error($code)
	{
		$this->{'error_' . $code}();
	}
	
	public function error_404()
	{
		echo 'ERROR 404';
	}
}