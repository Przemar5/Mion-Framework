<?php

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;
use Core\Classes\Model;


class ExistsValidator extends Validator
{
	public $value, $model, $table, $column;
	
	
	public function __construct($data)
	{
		parent::__construct();
		
		$this->value = $data['value'] ?? $data['args'][0];
		$this->table = $data['table'] ?? $data['args'][1];
		$this->column = $data['column'] ?? $data['args'][2];
		$this->errorMsg = $data['msg'];
		$this->model = new Model($this->table);
	}
	
	public function validate($value = null)
	{
		$this->success = true;
		$this->value = (!empty($value)) ? $value : $this->value;
		$data = [
			'bind' => [$value],
			'conditions' => $this->column . ' = ?'
		];
		$result = $this->model->findFirst($data);
		
		if (!$result)
		{
			$this->success = false;
		}
		
		return $this->success;
	}
}