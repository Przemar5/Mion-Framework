<?php

declare(strict_types = 1);

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;


class CustomValidator extends Validator
{
	public function __construct(array $params)
	{
		parent::__construct($params);
	}
	
	public function validate()
	{
		
	}
}
	