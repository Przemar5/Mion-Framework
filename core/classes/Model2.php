<?php

namespace Core\Classes;
use Core\Classes\Database;
use App\Models\UserModel;


class Model2
{
	protected Database2 $_db;
	protected string $_table;
	protected string $_modelName;
	protected string $_primaryKey = 'id';
	protected bool $_softDelete = false;
	protected array $_tableData;
			
	
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

	public function getTableData()
	{
		if (!empty($this->_tableData))
		{
			$result = [];

			foreach ($this->_tableData as $column)
			{
				$result[$column] = $this->{$column};
			}

			return $result;
		}
		else
		{
			return [];
		}
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

	public function insert($data = [], $check = true)
	{
		if (empty($data))
		{
			$data = $this->getTableData();
		}

		if (($check && $this->validate($data)) || !$check)
		{
			return $this->_db->insert($this->_table, $data);
		}
	}

	public function update($data = [], $check = true)
	{
		if (empty($data))
		{
			$data = $this->getTableData();
		}

		if ((($check && $this->validate($data)) || !$check) && 
			!empty($this->{$this->_primaryKey}))
		{
			return $this->_db->update($this->_table, $data);
		}
	}

	public function save($data = [])
	{
		// I know that judging if the record already exists in database
		// by checking if it has primary key is not good idea
		if (!empty($this->{$this->_primaryKey}))
		{
			return $this->_db->update($this->_table, $data);
		}
		else
		{

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