<?php

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;


class MatchValidator extends Validator
{
	public $first, $second;
	
	
	public function __construct($data)
	{
		parent::__construct();
		
		$this->first = $data['first'] ?? $data['args'][0];
		$this->second = $data['second'] ?? $data['args'][1] ?? null;
		$this->errorMsg = $data['msg'];
	}
	
	public function validate($second = null)
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