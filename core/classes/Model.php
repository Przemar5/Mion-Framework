<?php

declare(strict_types = 1);

namespace Core\Classes;
//	??
//use Core\ModelMediator;
//use Core\Exceptions\{InvalidTypeException, PropertyNotFoundException};
use Core\Classes\JSONHelper;
use Core\Classes\TypeHelper;
use Core\Classes\Validation;


class Model
{
	protected 	$_db, 
				$_table, 
				$_modelName,
				$_primaryKey = 'id', 
				$_softDelete = false, 
				$_tableProperties = [],
				$_validationRules = [],
				$_validates = true, 
				$_validationErrors = [],
				$_dependencies = null,
				$_checkDependencies = [],
				$_validationRulesPath = ROOT . DS. 'app' . DS . 'models' . DS . 'validation';
	
	
	public function __construct($table, $softDelete = true)
	{
		$this->_db = Database::getInstance();
		$this->_table = $table;
		$this->_softDelete = $softDelete;
		$this->_modelName = $this->tableToModelName($table);
		$this->_checkDependencies = ['select'];
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
	
	public function tableToModelName($table)
	{
		return implode('', array_map('ucfirst', explode('_', $table)));
	}
	
	public function debugDumpParams()
	{
		$this->_db->debugDumpParams();
	}
	
	/**
	 * Function for returning prepared InvalidArguentException with filename and line number in it's message.
	 *
	 * args:	argument of any type
	 * return:	InvalidTypeException
	 */
	protected function _invalidArgTypeException($arg, $types = ['string', 'integer', 'float'])
	{
		$backtrace = debug_backtrace()[0];
		
		$types = ($types) ? implode(', ', array_map(function($v) {	return "'" . $v . "'";	}, $types)) : '';
		$fileName = $backtrace['file'];
		$lineInfo = $backtrace['line'];
		$message = '<strong>' . $fileName . '</strong>. Line: <strong>' . $lineInfo . '</strong>: Given argument is invalid type (' . gettype($arg) . '). Valid types are: ' . $types . '.';
		
		return new Exceptions\InvalidTypeException($message);
	}
	
	protected function _softDeleteParams(array $params)
	{
		if ($this->_softDelete)
		{
			if (array_key_exists('conditions', $params))
			{
				try 
				{
					if (is_array($params['conditions']))
					{
						$params['conditions'][] = 'deleted != 1';
					}
					else if (is_string($params['conditions']))
					{
						$params['conditions'] .= ' AND deleted != 1';
					}
					else
					{
						throw TypeHelper::getException($params['conditions'], ['array', 'string']);
					}
				}
				catch (InvalidTypeException $e)
				{
					die($e->getMessage());
				}
			}
			else 
			{
				$params['conditions'] = 'deleted != 1';
			}
		}
		
		return $params;
	}
	
	public function find($params = [])
	{
		$params = $this->_softDeleteParams($params);
		$results = [];
		$resultsQuery = $this->_db->find($this->_table, $params, get_class($this));
		
		if (!$resultsQuery) {
			return [];
		}

		return $resultsQuery;
	}
	
	public function selectWithDependencies($params = [])
	{
		$dependencies = $this->loadDependencies('select');
		
		$params['select'] = 'id, name';
		
		try
		{
			if (!empty($params['select']))
			{
				if (is_string($params['select']))
				{
					$params['select'] = array_map('trim', explode(', ', $params['select']));
				}

				if (is_array($params['select']))
				{
//					$params['select'] = array_map(function($v) {
//						return $this->_table . '.' . $v;
//					}, $params['select']);
				}
				else
				{
					throw TypeHelper::getException($params['conditions'], ['array', 'string']);
				}
			}
			
			$params['join'] = '';
			
			foreach ($dependencies as $table => $part)
			{
				$params['join'] .= ' LEFT JOIN ' . $table . ' ON (';
				
				foreach ($part->where as $v1 => $v2)
				{
					if (!empty($params['conditions']))
					{
						if (!TypeHelper::valid($params['conditions'], ['array', 'string']))
						{
							throw TypeHelper::getException($params['conditions'], ['array', 'string']);
						}
					}
					
					if (!empty($v1) && !empty($v2))
					{
						$validTypes = ['string', 'integer', 'float'];
						
						if (!property_exists(get_class($this), $v1))
						{
							throw new PropertyNotFoundException("Property '$v1' not found in class " . get_class($this) . ".");
						}
						
						if (!TypeHelper::valid($v1, $validTypes))
						{
							throw TypeHelper::getException($v1, $validTypes);
						}
						
						if (is_array($v2))
						{
							$end = count($v2) - 1;
							
							for ($i = 0; $i < $end; $i++)
							{
								$params['join'] .= $this->_table . '.' . $v1 . ' = ' . $table . '.' . $v2[$i] . ' AND ';
							}
							
							$v2 = $v2[$end];
						}
						else if (!TypeHelper::valid($v2, $validTypes))
						{
							throw TypeHelper::getException($v2, $validTypes);
						}
						
						$params['join'] .= $this->_table . '.' . $v1 . ' = ' . $table . '.' . $v2 . ' AND ';
					}
				}
				
				$params['join'] = preg_replace('/ AND $/', ')', $params['join']);
			}
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
		catch (PropertyNotFoundException $e)
		{
			die($e->getMessage());
		}
		
		return $params;
	}
	
	public function findFirst($params = [], $class = true)
	{
		$params = $this->_softDeleteParams($params);
		$class = (!empty($class)) ? get_class($this) : null;
		$resultsQuery = $this->_db->findFirst($this->_table, $params, $class);
		
		return $resultsQuery;
	}
	
	public function findById($id)
	{
		$params = [
			'conditions' => $this->_primaryKey . ' = ?', 
			'bind' => [$id],
		];
		if (in_array('select', $this->_checkDependencies))
			$params = $this->selectWithDependencies($params);
		
		return $this->findFirst($params);
	}
	
	public function loadValidationRules($file = null)
	{
		$file = (!empty($file)) ? $file : $this->_table;
		$path = $this->_validationRulesPath . DS . $file . '.php';
		
		if (is_readable($path))
		{
			require_once $path;
			
			$this->_validationRules = $rules;
		}
	}
	
	public function getTableFieldsArray($properties = [])
	{
		if (empty($properties))
			$properties = $this->_tableProperties;
		
		$array = [];
		
		foreach ($properties as $property)
		{
			$array[$property] = $this->{$property};
		}
		
		return $array;
	}
	
	public function save($properties = [], $validate = false)
	{
		if ($validate)
			$this->validate($properties);
		
		if (!$validate || ($validate && $this->_validates))
		{
			$this->beforeSave();
			$fields = $this->getTableFieldsArray($properties);
			$action = '';
			
			// Determine whether to update or insert
			if (property_exists($this, $this->_primaryKey) && !empty($this->{$this->_primaryKey}))
			{
				$save = $this->update((int) $this->{$this->_primaryKey}, $fields);
				$action = 'UPDATE';
			}
			else
			{
				$save = $this->insert($fields);
				$action = 'INSERT';
			}
			
			//$this->afterSave($action);

			return $save;
		}
		return false;
	}
	
	public function beforeSave() 
	{
		
	}
	
	public function afterSave(string $action = '') 
	{
		if (empty($action) || empty($this->loadDependencies($action)))
		{
			return;
		}
		
		if (!$this->modifyDependencies($action))
		{
			return;
		}
	}
	
	public function validate($properties = [])
	{
		$this->_validates = false;
		if (empty($this->_validationRules))
			$this->loadValidationRules();
		
		$validation = new Validation;
		$data = $this->getTableFieldsArray($properties);
		
		if ($validation->make($data, $this->_validationRules))
			$this->_validates = true;
		else
			$this->_validationErrors = $validation->errors;

		return $this->_validates;
	}
	
	public function insert($fields)
	{
		if (empty($fields))
		{
			return false;
		}
		return $this->_db->insert($this->_table, $fields);
	}
	
	public function update($id = 0, $fields)
	{
		$id = (!empty($id)) ? (int) $id : (int) $this->{$this->_primaryKey};
		
		if (empty($fields) || empty($id))
		{
			return false;
		}
		return $this->_db->update($this->_table, $id, $fields);
	}
	
	public function delete($id = 0)
	{
		$id = (!empty($id)) ? (int) $id : (int) $this->{$this->_primaryKey};
		
		if (empty($id))
		{
			return false;
		}
		
		if ($this->_softDelete)
		{
			return $this->update($id, ['deleted' => 1]);
		}
		return $this->_db->delete($this->_table, $id);
	}
	
	public function query($sql, $bind = [])
	{
		return $this->_db->query($sql, $bind);
	}
	
	public function loadDependencies($action = 'insert')
	{
		if (empty($this->_dependencies))
		{
			$path = ROOT . DS . 'app' . DS . 'models' . DS . 'dependencies' . DS . strtolower($this->_table) . '.json';
			
			if (is_readable($path))
			{
				$content = file_get_contents($path);
				$this->_dependencies = json_decode(rtrim(preg_replace('/[\r\n\t\ ]/m', '', $content), '\0'));
			}
		}
		
		return $this->_dependencies->{strtolower($action)} ?? null;
	}
	
	public function modifyDependencies($action)
	{
		foreach ($this->dependencies[$action] as $table => $methods)
		{
			$model = $this->tableToModelName($table);
			
			foreach ($methods as $method => $values)
			{
				$params = [];
				
				foreach ($values as $key => $value)
				{
					$params[$key] = $this->{$value};
				}
				
	 			if (!ModelMediator::make($model, $method . 'By', [$params]))
	 			{
	 				return false;
	 			}
			}
		}

		return true;
	}
	
	public function assign($params)
	{
		if (!empty($params))
		{
			foreach ($params as $key => $value)
			{
				if (property_exists($this, $key))
				{
					$this->$key = $value;
				}
			}
			return true;
		}
		return false;
	}
	
	protected function populateObjData($result)
	{
		foreach ($result as $key => $value)
		{
			if (property_exists(get_class($this), $key))
				$this->key = $value;
		}
	}
	
	public function getErrorMessages() 
	{
		return $this->_validationErrors;
	}
	
	public function validationPassed()
	{
		return $this->_validates;
	}
	
	public function addErrorMessage($field, $msg)
	{
		$this->_validates = false;
		$this->_validationErrors[$field] = $msg;
	}
	
	public function isNew()
	{
		return (property_exists($this, 'id') && !empty($this->id)) ? false : true;
	}
}