<?php

namespace Core\Classes;
use Core\Classes\Validators\RequiredValidator;


class Validation
{
	public $passed = true, $errors = [];
	
	
	public function __construct()
	{
		
	}
	
	public function make($data, $rules, $multiple = true)
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
	
	public function getValidatorName($validator)
	{
		return 'Core\Classes\Validators\\' . $validator . 'Validator';
	}
}