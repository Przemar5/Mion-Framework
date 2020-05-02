<?php

declare(strict_types = 1);

namespace Core\Classes;
use Core\Classes\Validators\RequiredValidator;


class Validation
{
	public bool $passed = true;
	public array $errors = [];
	
	
	public function __construct()
	{
		
	}
	
	public function make(array $data, array $rules = [], ?bool $multiple = true): bool
	{
		$this->passed = true;
		$this->errors = [];
		
		foreach ($rules as $key => $validatorsArray)
		{
			foreach ($validatorsArray as $validatorName => $validatorData)
			{
				$validatorName = implode('', array_map('ucfirst', explode('-', $validatorName)));
				$validatorWithNamespace = $this->getValidatorName($validatorName);
				array_unshift($validatorData['args'], $data[$key]);
				$validator = new $validatorWithNamespace($validatorData);

				if (!$validator->validate())
				{
					$this->passed = false;
					$this->errors[$key] = $validator->errorMsg;
					
					if ($multiple)
						break 1;
					else
						break 2;
				}
			}
		}
		
		return $this->passed;
	}
	
	public function getValidatorName(string $validator): string
	{
		return 'Core\Classes\Validators\\' . $validator . 'Validator';
	}
}