<?php

declare(strict_types = 1);

namespace App\Controllers;
use Core\Classes\Controller;


class ErrorController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function error(int $code): void
	{
		$this->{'error_' . $code}();
	}
	
	public function error_404(): void
	{
		echo 'ERROR 404';
	}
}