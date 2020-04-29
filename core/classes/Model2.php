<?php

namespace Core\Classes;
use Core\Classes\Database;
use App\Models\UserModel;


class Model2
{
	private $_db,
			$_table,
			$_modelName,
			$_primaryKey = 'id',
			$_softDelete = false;
			
	
	public function __construct($table)
	{
		$this->_db = Database2::getInstance();
		$this->_table = $table;
		$this->_softDelete = true;
	}
	
	public static function load($modelName)
	{
		$model = self::fullName($modelName);
		
		return new $model;
	}
	
	public static function fullName($model)
	{
		return MODELS_NAMESPACE . $model . 'Model';
	}
	
	public function find($data)
	{
		if ($result = $this->_db->select($this->_table, $data))
		{
			$this->_existsInDatabase = true;
		}
	}
	
	public function findFirst($data)
	{
		$data['limit'] = 1;
		
		return $this->_db->select($this->_table, $data, true, ['multiple' => false]);
	}

	public function insert($data = [])
	{

	}

	public function save($data = [])
	{
		if (!empty($this->{$this->_primaryKey}))
		{
			return $this->_db->update($this->_table, $data);
		}
		
	}
	
	public function delete($data = [])
	{
		if (!empty($data))
		{
			return $this->_db->delete($this->_table, $data);
		}
		else if (!empty($this->{$this->_primaryKey}))
		{
			$data = [
				'conditions' => $this->_primaryKey . ' = ?',
				'bind' => [$this->{$this->_primaryKey}]
			];
			
			return $this->_db->delete($this->_table, $data);
		}
		else
		{
			return false;
		}
	}
	
	public function deleteWhere($values)
	{
		$data = [
			'conditions' => '',
			'bind' => []
		];
		
		foreach ($values as $field => $value)
		{
			$data['conditions'] .= $field . ' = ? AND ';
			array_push($data['bind'], $value);
		}
		
		if (!empty($data['conditions']))
			preg_replace('/ AND $/', '', $data['conditions']);
		
		return $this->_db->delete($this->_table, $data);
	}
}