<?php

declare(strict_types = 1);

namespace Core\Classes\Validators;
use Core\Abstracts\Validator;
use Core\Classes\Model;


class UniqueValidator extends Validator
{
	public $value;
	public Model $model;
	public string $table;
	public string $column;
	
	
	public function __construct(array $data)
	{
		parent::__construct();
		
		$this->value = $data['value'] ?? $data['args'][0];
		$this->table = $data['table'] ?? $data['args'][1];
		$this->column = $data['column'] ?? $data['args'][2];
		$this->errorMsg = $data['msg'];
		$this->model = new Model($this->table);
	}
	
	public function validate($value = null): bool
	{
		$this->success = true;
		$this->value = (!empty($value)) ? $value : $this->value;
		$data = [
			'bind' => [$this->value],
			'conditions' => $this->column . ' = ?'
		];
		
		$result = $this->model->findFirst($data, false);
		
		if ($result)
		{
			$this->success = false;
		}
		
		return $this->success;
	}
}