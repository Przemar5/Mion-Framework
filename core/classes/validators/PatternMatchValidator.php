<?php

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;


class PatternMatchValidator extends Validator
{
	public $pattern, $value;
	
	
	public function __construct($data)
	{
		$this->value = $data['value'] ?? $data['args'][0];
		$this->pattern = $data['pattern'] ?? $data['args'][1];
		$this->errorMsg = $data['msg'];
	}
	
	public function validate($value = null)
	{
		$this->success = true;
		$this->value = (!empty($value)) ? $value : $this->value;
		
		$result = preg_match($this->pattern, $this->value);
		
		if (!$result)
		{
			$this->success = false;
			return $result;
		}
		
		return $this->success;
	}
}