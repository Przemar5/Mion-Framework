<?php


namespace Core\Classes;
use \PDO;
use \PDOException;


class Database2
{
	private static ?self $_instance = null;
	private const DB_TYPE = 'mysql',
				  DB_HOST = '127.0.0.1',
				  DB_USERNAME = 'root',
				  DB_PASSWORD = '',
				  DB_NAME = 'play_and_learn',
				  DB_CHARSET = 'utf8';
	private \PDO $_pdo;
	private \PDOStatement $_stmt;
	private mixed $_result;
	private bool $_error = false;
	private int $_count;
	private int $_lastInsrtId;
	private string $_lastExecutedQuery;
	private array $_queryParts;
	
	
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
	
	public static function getInstance(): self
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new self;
		}
		
		return self::$_instance;
	}
	
	public function debugDumpParams(): void
	{
		$this->_stmt->debugDumpParams();
	}
	
	public function select(string $table, array $data, bool $fetch = true, array $fetchOptions = [], bool $checkBeforeFetch = false): mixed
	{
		try
		{
			$this->_error = false;
			
			if ($query = $this->buildSelectQuery($table, $data))
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
							$this->_stmt->bindValue($i++, $param);
						}
					}
				}

				if ($this->_stmt->execute())
				{
					$this->_lastExecutedQuery = $query;

					if ($fetch)
					{
						return $this->fetch($fetchOptions['mode'] ?? null, 
											$fetchOptions['class'] ?? null,
											$fetchOptions['multiple'] ?? null,
											$checkBeforeFetch);
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
	
	public function fetch(int $mode = PDO::FETCH_OBJ, ?string $class, ?bool $multiple = true, bool $check = true): mixed
	{
		try
		{
			if ($check && $this->checkmode($mode))
			{
				$class = $this->prepareClass($class);
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
	
	public function checkMode(int $fetchMode): bool
	{
		if (!in_array($fetchMode, [
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
	
	public function prepareClass(string $class): string
	{
		$class = trim($class);

		if (!class_exists($class))
		{
			throw new \Exception("Class '$class' does not exist in available namespaces.");
		}
		else 
		{
			return $class;
		}
	}
	
	public function insert(string $table, array $data): bool
	{
		$query = $this->buildInsertQuery($table, array_keys($data));
		
		return $this->query($query, array_values($data));
	}
	
	public function update(string $table, string $mainKey, int $keyValue, array $data): mixed
	{
		$query = $this->buildUpdateQuery($table, $mainKey, array_keys($data));
		
		try
		{
			if (isset($data['bind']))
			{
				if (!is_array($data['bind']))
				{
					throw new \Exception("Values to bind must be an 'array' type," .
										" not " . gettype($data['bind']) . ".");
				}
				else 
				{
					$data['values'] = array_merge(array_values($data['values']), $data['bind'], $keyValue);
				}
			}
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
		
		if ($this->query($query, $data['values']))
		{
			return $this->_count;
		}
		else 
		{
			return false;
		}
	}
	
	public function delete(string $table, array $data): mixed
	{
		$query = $this->buildDeleteQuery($table, $data);
		
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
		
		if ($this->query($query, $data['bind']))
		{
			return $this->_count;
		}
		else 
		{
			return false;
		}
	}
	
	public function execute(string $query)
	{
		$this->_stmt = $this->_pdo->prepare($query);
		$this->_stmt->execute();
		$this->_result = $this->fetch(PDO::FETCH_OBJ);
		$this->_count = $this->_stmt->rowCount();
		
		return $this->_result;
	}
	
	public function query(string $query, array $params = []): bool
	{
		try
		{
			if (!$this->_stmt = $this->_pdo->prepare($query))
			{
				throw new \Exception('Could not prepare query.');
			}
			
			if (!empty($params))
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
				$this->_lastExecutedQuery = $query;
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
	
	public function buildSelectQuery(string $table, array $data): string
	{
		extract($data);
		
		try
		{
															$query = 'SELECT';
			if (isset($values) && !empty($values)) 			$query .= $this->_getValuesQueryPart($values);
															$query .= ' FROM ';
															$query .= $this->_getTableQueryPart($table);
			if (isset($conditions) && !empty($conditions)) 	$query .= $this->_getConditionsQueryPart($conditions);
			if (isset($limit) && !empty($limit)) 			$query .= $this->_getLimitQueryPart($limit);
			if (isset($offset) && !empty($offset)) 			$query .= $this->_getOffsetQueryPart($offset);
			
			return $query;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildInsertQuery(string $table, array $data): string
	{
		try
		{
			$fieldsString = ' (';
			$valuesString = ' VALUES (';
			
			foreach ($data as $key)
			{
				$fieldsString .= '`' . $key . '`, ';
				$valuesString .= '?, ';
			}
			
			$fieldsString = preg_replace('/, $/', ') ', $fieldsString);
			$valuesString = preg_replace('/, $/', ') ', $valuesString);
			
			$query = 'INSERT INTO ';
			$query .= $this->_getTableQueryPart($table);
			$query .= $fieldsString . $valuesString;
			
			return $query;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildUpdateQuery(string $table, string $mainKey, array $data): string
	{
		try
		{
		    $valuesString = implode(',', array_map(fn(string $a) => ' ' . $a . ' = ?'));

			$query = 'UPDATE ';
			$query .= $this->_getTableQueryPart($table);
			$query .= ' SET' . $valuesString;
			$query .= ' WHERE ' . $mainKey . ' = ?';
			
			return $query;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildDeleteQuery(string $table, array $data): string
	{
		extract($data);
		
		try
		{
															$query = 'DELETE FROM ';
															$query .= $this->_getTableQueryPart($table);
			if (isset($conditions) && !empty($conditions)) 	$query .= $this->_getConditionsQueryPart($conditions);
			if (isset($limit) && !empty($limit)) 			$query .= $this->_getLimitQueryPart($limit);
			if (isset($offset) && !empty($offset)) 			$query .= $this->_getOffsetQueryPart($offset);
			
			return $query;
		}
		catch (\Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function buildQuery(array $data, array $queryParts = []): string
	{
		$query = '';
		
		foreach ($queryParts as $queryPart)
		{
			if (array_key_exists($queryPart, $data))
			{
				$methodName = '_get' . ucfirst($queryPart) . 'QueryPart';
				
				if (method_exists($this, $methodName))
				{
					$query .= $this->{$methodName}($data[$queryPart]);
				}
			}
		}
		
		return $query;
	}
	
	private function _getValuesQueryPart($values): string
	{
		if (is_string($values))
		{
			$values = trim($values);

			return (!empty($values)) ? (' ' . $values) : ' *';
		}
		else if (is_array($values))
		{
			// $reduceCallable = function(string $carry, string $item) {
			// 	$item = trim($item);
			// 	return (!empty($item)) ? ($carry . ', ' . $item) : $carry;
			// };

			// return ' ' . ltrim(array_reduce($arr, 'reduceCallable'), ', ');

			return ' ' . implode(', ', array_map('trim', array_filter($values, fn(string $a) => !empty(trim($a)))));
		}
		else 
		{
			throw new \Exception("Values must be type 'string' or " . 
								 "'array', not " . gettype($values) . ".");
		}
	}
	
	private function _getTableQueryPart(string $table): string
	{
		$table = trim($table);
		
		if (empty($table))
		{
			throw new \Exception('Table name is required.');
		}
		
		return $table;
	}
	
	private function _getConditionsQueryPart(string $conditions): string
	{
		return ' WHERE ' . $conditions;
	}
	
	private function _getLimitQueryPart(int $limit): string
	{
		if ($limit < 0)
		{
			throw new \Exception('Limit must be equal or greater than 0.');
		}

		return ' LIMIT ' . $limit;
	}
	
	private function _getOffsetQueryPart(int $offset): string
	{
		if ($offset < 0)
		{
			throw new \Exception('Offset must be equal or greater than 0.');
		}

		return ' OFFSET ' . $offset;
	}
	
	public function getLastExecutedQuery(): ?string
	{
		return $this->_lastExecutedQuery;
	}
	
	public function lastInsertId()
	{
		return $this->_lastInsertId;
	}
}