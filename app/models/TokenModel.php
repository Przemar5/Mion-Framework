<?php

declare(strict_types = 1);

namespace App\Models;
use Core\Classes\Model;


class TokenModel extends Model
{
	public function __construct()
	{
		parent::__construct('token', false);
		
		$this->_softDelete = false;
		$this->_primaryKey = 'id';
	}
	
	public function findByToken(string $name, $value, ?bool $class = false)
	{
		$regex = '/^[0-9a-zA-Z\/\_\-\+\=]+$/';
		
		if (preg_match($regex, $name) && preg_match($regex, $value))
		{
			return $this->findFirst([
				'values' => 'user_id, id',
				'bind' => [$name, $value],
				'conditions' => 'name = ? AND value = ?'
			], $class);
		}
	}
	
	public function deleteWhere($values)
	{
		return $this->_db->deleteBy($this->_table, $values);
	}
	
//	public function insert($name, $value, $userId)
//	{
//		$sql = "INSERT INTO $this->_table (name, value, user_id) " . 
//				" VALUES ('$name', '$value', '$userId');";
//		$this->_db->query($sql);
//	}
}