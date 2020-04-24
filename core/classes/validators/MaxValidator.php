<?php

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;


class MaxValidator extends Validator
{
	public $value, $max;
	
	
	public function __construct($data)
	{
		parent::__construct();
		
		$this->value = $data['value'] ?? $data['args'][0];
		$this->max = $data['max'] ?? $data['args'][1];
		$this->errorMsg = $data['msg'];
	}
	
	public function validate($value = null)
	{
		$this->success = true;
		$this->value = (!empty($value)) ? $value : $this->value;
		
		if (is_string($this->value) && strlen($this->value) > $this->max)
		{
			$this->success = false;
		}
		else if ((is_integer($this->value) || is_float($this->value)) &&
				 $this->value > $this->max)
		{
			$this->success = false;
		}
		
		return $this->success;
	}
}