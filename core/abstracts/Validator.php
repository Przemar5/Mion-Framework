<?php

namespace Core\Abstracts;
use Core\Interfaces\ValidateInterface;


abstract class Validator implements ValidateInterface
{
	public $success = true, $errorMsg = '';
	
	public function __construct()
	{
		//
	}
	
	abstract public function validate($data = null);
}