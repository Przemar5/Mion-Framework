<?php

declare(strict_types = 1);

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;


class PatternMatchValidator extends Validator
{
	public string $pattern;
	public $value;
	
	
	public function __construct(array $data)
	{
		$this->value = $data['value'] ?? $data['args'][0];
		$this->pattern = $data['pattern'] ?? $data['args'][1];
		$this->errorMsg = $data['msg'];
	}
	
	public function validate($value = null): bool
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