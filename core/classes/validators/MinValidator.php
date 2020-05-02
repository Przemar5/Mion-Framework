<?php

declare(strict_types = 1);

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;


class MinValidator extends Validator
{
	public $value;
	public int $min;
	
	
	public function __construct(array $data)
	{
		parent::__construct();
		
		$this->value = $data['value'] ?? $data['args'][0];
		$this->min = $data['min'] ?? $data['args'][1];
		$this->errorMsg = $data['msg'];
	}
	
	public function validate($value = null): bool
	{
		$this->success = true;
		$this->value = (!empty($value)) ? $value : $this->value;
		
		if (is_string($this->value) && strlen($this->value) < $this->min)
		{
			$this->success = false;
		}
		else if ((is_integer($this->value) || is_float($this->value)) &&
				 $this->value < $this->min)
		{
			$this->success = false;
		}
		
		return $this->success;
	}
}