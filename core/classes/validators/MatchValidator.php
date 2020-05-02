<?php

declare(strict_types = 1);

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;


class MatchValidator extends Validator
{
	public string $first;
	public $second;
	
	
	public function __construct(array $data)
	{
		parent::__construct();
		
		$this->first = $data['first'] ?? $data['args'][0];
		$this->second = $data['second'] ?? $data['args'][1] ?? null;
		$this->errorMsg = $data['msg'];
	}
	
	public function validate($second = null): bool
	{
		$this->success = true;
		$this->second = (!empty($second)) ? $second : $this->second;
		
		if (strcmp($this->first, $this->second))	
		{
			$this->success = false;
		}
		
		return $this->success;
	}
}