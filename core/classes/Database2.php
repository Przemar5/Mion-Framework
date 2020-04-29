<?php


namespace Core\Classes;
use \PDO;
use \PDOException;


class Database2
{
	private static $_instance = null;
	private const DB_TYPE = 'mysql',
				  DB_HOST = '127.0.0.1',
				  DB_USERNAME = 'root',
				  DB_PASSWORD = '',
				  DB_NAME = 'play_and_learn',
				  DB_CHARSET = 'utf8';
	private $_pdo, 
			$_query = '', 
			$_stmt,
			$_result,
			$_error = false,
			$_count,
			$_lastInsrtId,
			$_queryParts;
	
	
	public function __construct()
	{
		$dsn = self::DB_TYPE . ':host=' . self::DB_HOST . ';dbname=' . 
				self::DB_NAME . ';charset=' . self::DB_CHARSET;
		$options = [
			PDO::ATTR_ERRMODE 				=> 	PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE 	=> 	PDO::FETCH_OBJ,
    		PDO::ATTR_EMULATE_PREPARES 		=> 	false,
		];
		
		try
		{
			$this->_pdo = new PDO($dsn, self::DB_USERNAME, self::DB_PASSWORD, $options);
		}
		catch (PDOException $e)
		{
			die($e->getMessage());
		}
	}
	
	public static function getInstance()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new self;
		}
		
		return self::$_instance;
	}
	
	public function debugDumpParams()
	{
		$this->_stmt->debugDumpParams();
	}
	
	public function select(string $table, $data, $fetch = true, $fetchOptions = [], $validate = false)
	{
		try
		{
			$this->_error = false;
			
			if ($this->buildSelectQuery($table, $data))
			{
				if ($fetch && $this->checkMode($fetchMode))
				{
					$class = $this->prepareClass($class);
				}

				if ($this->_stmt = $this->_pdo->prepare($this->_query))
				{
					$i = 1;

					if (is_array($data['bind']) && !empty($data['bind']))
					{
						foreach ($data['bind'] as $param)
						{
							$this->_stmt->bindValue($i, $param);
							$i++;
						}
					}
				}

				if ($this->_stmt->execute())
				{
					if ($fetch)
					{
						return $this->fetch($fetchOptions['mode'] ?? null, 
											$fetchOptions['class'] ?? null,
											$fetchOptions['multiple'] ?? null,
											$validate);
					}
				}
				else
				{
					$this->_error = true;
				}
				
				return !$this->_error;
			}
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function fetch($mode, $class = false, $multiple = true, $check = true)
	{
		try
		{
			if ($check)
			{
				if ($this->checkmode($mode))
				{
					$class = $this->prepareClass($class);
				}
			}
			
			$fetchFunction = ($multiple) ? 'fetchAll' : 'fetch';
			
			$this->_result = ($class) 
				? $this->_stmt->{$fetchFunction}($mode, $class)
				: $this->_stmt->{$fetchFunction}($mode);
			
			$this->_count = $this->_stmt->rowCount();
			
			return $this->_result;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function checkMode($fetchMode = null)
	{
		if (!is_integer($fetchMode))
		{
			throw new \Exception("fetch mode must be type 'integer'," . 
								" not " . gettype($fetchMode) . ".");
		}
		else if (!in_array($fetchMode, [
				   PDO::FETCH_ASSOC,
				   PDO::FETCH_BOTH,
				   PDO::FETCH_BOUND,
				   PDO::FETCH_CLASS,
				   PDO::FETCH_INTO,
				   PDO::FETCH_LAZY, 
				   PDO::FETCH_NAMED,
				   PDO::FETCH_NUM,
				   PDO::FETCH_OBJ
			   ]))
		{
			throw new \Exception('Given fetch mode is invalid.');
		}
		
		return true;
	}
	
	public function prepareClass($class = false)
	{
		if (!empty(trim($class)))
		{
			if (!is_string($class))
			{
				throw new \Exception("Class name must be of type 'string'," . 
									" not " . gettype($class) . ".");
			}
			else if (!class_exists($class))
			{
				throw new \Exception("Class '$class' does not exist in available namespaces.");
			}

			return trim($class);
		}
		
		return false;
	}
	
	public function insert(string $table, $data)
	{
		$this->buildInsertQuery($table, $data);
		
		return $this->query($this->_query, array_values($data));
	}
	
	public function update(string $table, $data)
	{
		$this->buildUpdateQuery($table, $data);
		
		try
		{
			if (isset($data['bind']))
			{
				if (!is_array($data['bind']))
				{
					throw new \Exception("Values to bind must be an 'array' type," .
										" not " . gettype($data['bind']) . ".");
				}
				$data['values'] = array_merge(array_values($data['values']), $data['bind']);
			}
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
		
		if ($this->query($this->_query, $data['values']))
		{
			return $this->_count;
		}
		return 2;
	}
	
	public function delete(string $table, $data)
	{
		$this->buildDeleteQuery($table, $data);
		
		try
		{
			if (isset($data['bind']))
			{
				if (!is_array($data['bind']))
				{
					throw new \Exception("Values to bind must be an 'array' type," .
										" not " . gettype($data['bind']) . ".");
				}
			}
			else
			{
				$data['bind'] = [];
			}
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
		
		if ($this->query($this->_query, $data['bind']))
		{
			return $this->_count;
		}
		return false;
	}
	
	public function execute($query)
	{
		$this->_stmt = $this->_pdo->prepare($query);
		$this->_stmt->execute();
		$this->_result = $this->fetch(PDO::FETCH_OBJ);
		$this->_count = $this->_stmt->rowCount();
		
		return $this->_result;
	}
	
	public function query($query, $params = [])
	{
		try
		{
			if (!$this->_stmt = $this->_pdo->prepare($query))
			{
				throw new \Exception('Could not prepare query.');
			}
			
			if (!empty($params) && is_array($params))
			{
				$i = 1;

				foreach ($params as $param)
				{
					$this->_stmt->bindValue($i, $param);
					$i++;
				}
			}

			if ($this->_stmt->execute())
			{
				$this->_lastInsertId = $this->_pdo->lastInsertId();
				$this->_count = $this->_stmt->rowCount();
			}
			else
			{
				$this->_error = true;
			}

			return !$this->_error;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildSelectQuery(string $table, $data)
	{
		extract($data);
		
		try
		{
			$this->_query = 'SELECT';
			$this->_query .= $this->_getValuesQueryPart($values ?? '');
			$this->_query .= ' FROM ';
			$this->_query .= $this->_getTableQueryPart($table);
			$this->_query .= $this->_getConditionsQueryPart($conditions ?? '');
			$this->_query .= $this->_getLimitQueryPart($limit ?? '');
			$this->_query .= $this->_getOffsetQueryPart($offset ?? '');
			
			return true;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildInsertQuery(string $table, $data)
	{
		extract($data);
		
		try
		{
			$fieldsString = " (";
			$valuesString = " VALUES (";
			
			foreach ($data as $key => $value)
			{
				$fieldsString .= "`" . $key . "`, ";
				$valuesString .= "?, ";
			}
			
			$fieldsString = preg_replace('/, $/', ') ', $fieldsString);
			$valuesString = preg_replace('/, $/', ') ', $valuesString);
			
			$this->_query = "INSERT INTO ";
			$this->_query .= $this->_getTableQueryPart($table);
			$this->_query .= $fieldsString . $valuesString;
			
			return true;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildUpdateQuery(string $table, $data)
	{
		extract($data);
		
		try
		{
			$valuesString = "";
			
			foreach ($values as $key => $value)
			{
				$valuesString .= " $key = ?,";
			}
			
			$valuesString = preg_replace('/,$/', ' ', $valuesString);
			
			$this->_query = "UPDATE ";
			$this->_query .= $this->_getTableQueryPart($table) . '';
			$this->_query .= " SET" . $valuesString;
			$this->_query .= $this->_getConditionsQueryPart($conditions ?? '');
			$this->_query .= $this->_getLimitQueryPart($limit ?? '');
			$this->_query .= $this->_getOffsetQueryPart($offset ?? '');
			
			return true;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildDeleteQuery(string $table, $data)
	{
		extract($data);
		
		try
		{
			$this->_query = 'DELETE FROM ';
			$this->_query .= $this->_getTableQueryPart($table);
			$this->_query .= $this->_getConditionsQueryPart($conditions ?? '');
			$this->_query .= $this->_getLimitQueryPart($limit ?? '');
			$this->_query .= $this->_getOffsetQueryPart($offset ?? '');
			
			return true;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildQuery($data = [], $queryParts = [])
	{
		$this->_query = '';
		
		if (empty($data))
		{
			return false;
		}
		
		foreach ($queryParts as $queryPart)
		{
			if (array_key_exists($queryPart, $data))
			{
				$methodName = '_get' . ucfirst($queryPart) . 'QueryPart';
				
				if (method_exists($this, $methodName))
				{
					$this->_query .= $this->{$methodName}($data[$queryPart] ?? '');
				}
			}
		}
		
		return $this->_query;
	}
	
	public function clearQuery()
	{
		$this->_query = '';
		$this->_action = '';
	}
	
	public function getQuery()
	{
		return $this->_query;
	}
	
	public function getAction()
	{
		return $this->_action;
	}
	
	private function _getActionQueryPart($action)
	{
		if (!is_string($action))
		{
			throw new \Exception("Action name must be type 'string'.");
		}
		
		$action = strtoupper(trim($action));

		if (empty($action))
		{
			throw new \Exception("Action name is required.");
		}

		if (in_array($action, ['SELECT', 'INSERT', 'UPDATE', 'DELETE']))
		{
			if (empty($this->_action))
			{
				$this->_action = $action;
			}
			return $action;
		}
		else 
		{
			throw new \Exception("Action has invalid name. Possible action names are: " . 
								"'SELECT', 'INSERT', 'UPDATE' and 'DELETE'.");
		}
	}
	
	private function _getValuesQueryPart($values = '')
	{
		if (empty($values) || (is_string($values) && empty(trim($values))))
		{
			return ' *';
		}
		else if (is_string($values))
		{
			return ' ' . trim($values);
		}
		else if (is_array($values))
		{
			$valuesString = '';

			foreach ($values as $value)
			{
				if (!is_string($value))
				{
					throw new \Exception("Values must be type 'string', " . 
										 "not " . gettype($value) . ".");
				}

				if (!empty(trim($value))) 
				{
					$valuesString .= trim($value) . ', ';
				}
			}

			if (empty($valuesString))
			{
				return false;
			}

			return ' ' . preg_replace('/, $/', '', $valuesString);
		}
		else 
		{
			throw new \Exception("Values must be type 'string' or " . 
								 "'array', not " . gettype($values) . ".");
		}
	}
	
	private function _getTableQueryPart($table)
	{
		if (!is_string($table))
		{
			throw new \Exception("Table name must be type 'string', not " . 
							 	gettype($table) . ".");
		}
		
		$table = trim($table);
		
		if (empty($table))
		{
			throw new \Exception("Table name is required.");
		}
		
		return $table;
	}
	
	private function _getConditionsQueryPart($conditions = '')
	{
		if (!is_string($conditions))
		{
			throw new \Exception("Conditions must be passed as a 'string', not" . 
								 gettype($conditions) . ".");
		}
		
		return (!empty(trim($conditions))) ? ' WHERE ' . trim($conditions) : '';
	}
	
	private function _getLimitQueryPart($limit = '')
	{
		if (empty($limit) || (is_string($limit) && empty(trim($limit))))
		{
			return '';
		}
		else if (is_integer($limit) || is_numeric($limit))
		{
			return ' LIMIT ' . $limit;
		}
		else 
		{
			throw new \Exception("Limit must be type 'integer' or " . 
								 "numeric 'string', not " . gettype($limit) . ".");
		}
	}
	
	private function _getOffsetQueryPart($offset = '')
	{
		if (empty($offset) || (is_string($offset) && empty(trim($offset))))
		{
			return '';
		}
		else if (is_integer($offset) || is_numeric($offset))
		{
			return ' OFFSET ' . $offset;
		}
		else 
		{
			throw new \Exception("Offset must be type 'integer' or " . 
								 "numeric 'string', not " . gettype($offset) . ".");
		}
	}
	
	public function lastInsertId()
	{
		return $this->_lastInsertId;
	}
}