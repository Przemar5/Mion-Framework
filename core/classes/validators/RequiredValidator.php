<?php

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;


class RequiredValidator extends Validator
{
	public $value;
	
	
	public function __construct($data)
	{
		parent::__construct();
		
		$this->value = $data['value'] ?? $data['args'][0];
		$this->errorMsg = $data['msg'];
	}
	
	public function validate($value = null)
	{
		$this->success = true;
		$this->value = (!empty($value)) ? $value : $this->value;
		
		if (empty($this->value))
		{
			$this->success = false;
		}
		
		return $this->success;
	}
}