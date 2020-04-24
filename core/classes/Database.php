<?php

//	To avoid checking everytime if query is a string, params aren't objects etc.
declare(strict_types = 1);

namespace Core\Classes;
use \PDO;
use \PDOException;
use Core\Exceptions\{ClassNotFoundException, InvalidTypeException};
use Core\Classes\TypeHelper;


class Database
{
	private static $_instance = null;
	private $_pdo, 
			$_query, 
			$_error = false, 
			$_result, 
			$_count = 0, 
			$_lastInsertId = null, 
			$_validQueryArguments;
	
	
	public function __construct()
	{
		try 
		{
			if (!$db = $this->loadConfig())
				exit;
			
			$dsn = $db['type'] . ':host=' . $db['host'] . ';dbname=' . $db['name'];
			$this->_pdo = new PDO($dsn, $db['username'], $db['password']);
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// Possible securite leak
			// https://stackoverflow.com/questions/22066243/php-does-pdo-quote-safe-from-sql-injection
			$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
		}
		catch (PDOException $e)
		{
			die($e->getMessage());
		}
	}
	
	/**
	 * Singleton design pattern: there could be only one database instance in our app.
	 *
	 * args:	null
	 * return:	null
	 */
	public static function getInstance()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new self;
		}
		
		return self::$_instance;
	}
	
	/**
	 * Load config file with database credentials.
	 *
	 * args:	null
	 * return:	array
	 */
	public function loadConfig()
	{
		$path = ROOT . DS . 'config' . DS . 'dbconfig.php';
		
		if (is_readable($path))
		{
			require_once($path);
			
			return [
				'type' => DB_TYPE,
				'host' => DB_HOST,
				'username' => DB_USERNAME,
				'password' => DB_PASSWORD,
				'name' => DB_NAME
			];
		}
		return false;
	}
	
	/**
	 * Print send SQL query for debugging purpose.
	 *
	 * args:	null
	 * return:	null
	 */
	public function debugDumpParams()
	{
		$this->_query->debugDumpParams();
	}
	
	/**
	 * Function for returning prepared ClassNotFoundException with filename and line number in it's message.
	 *
	 * args:	string
	 * return:	ClassNotFoundException
	 */
	private function _classNotFoundException(string $class)
	{
		$backtrace = debug_backtrace()[0];
		
		$fileName = $backtrace['file'];
		$lineInfo = $backtrace['line'];
		$message = '<strong>' . $fileName . '</strong>. Line: <strong>' . $lineInfo . '</strong>: Class (' . $class . ') not found.';
		
		return new Exceptions\ClassNotFoundException($message);
	}
	
	/**
	 * Prepare and execute query.
	 *
	 * args:	string, array, string
	 * return:	self (Database object)
	 */
	public function query(string $sql, $params = [], $class = '')
	{
		$this->_error = false;
		
		try 
		{
			if (!empty($class))
			{
				if(!is_string($class))
				{
					throw TypeHelper::getException($class, ['string']);
				}
				else if (!class_exists($class))
				{
					throw $this->_classNotFoundException($class);
				}
			}
			
			if (!empty($params) && !is_array($params))
			{
				throw TypeHelper::getException($params, ['array']);
			}
			
			if ($this->_query = $this->_pdo->prepare($sql))
			{
				$i = 1;

				if (!empty($params) && is_array($params))
				{
					foreach ($params as $param)
					{
//						if (!TypeHelper::valid($param, ['string', 'integer', 'float']))
//						{
//							throw TypeHelper::getException($param, true);
//						}
						
						$this->_query->bindValue($i, $param);
						$i++;
					}
				}
			}
			
			if ($this->_query->execute())
			{
				if (!empty($class))
				{
					if (preg_match('/^select/mi', $sql))
					{
						$this->_result = $this->_query->fetchAll(PDO::FETCH_CLASS, $class);
					}
					else
					{
						$this->_result = true;
					}
				}
				else 
				{
					if (preg_match('/^select/mi', $sql))
					{
						$this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
					}
					else
					{
						$this->_result = true;
					}
				}
				
				$this->_count = $this->_query->rowCount();
				$this->_lastInsertId = $this->_pdo->lastInsertId();
			}
			else
			{
				$this->_error = true;
			}
		}
		catch (ClassNotFoundException $e)
		{
			die($e->getMessage());
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
		catch (PDOException $e)
		{
			die($e->getMessage());
		}
		
		return $this;
	}
	
	/**
	 * Read results of send SQL select query
	 *
	 * args:	string, array, string
	 * return:	boolean
	 */
	protected function _read(string $table, array $params = [], $class = '') 
	{
		try
		{
			if (!empty($class) && !class_exists($class))
			{
				throw $this->_classNotFoundException($class);
			}
		}
		catch (Exceptions\ClassNotFoundException $e)
		{
			die($e->getMessage());
		}
		
		$bind = (array_key_exists('bind', $params)) ? $params['bind'] : [];
		$getParams = ['values', 'conditions', 'order', 'limit', 'offset'];
		$query = $this->_buildQuery($table, $params, $getParams);
		
		if ($this->query($query, $params['bind'], $class))
		{
			if (!count($this->_result))
			{
				return false;
			}
			else 
			{
				return true;
			}
		}
	}
	
	public function buildQuery($table, $params, $parts = [])
	{
		return $this->_buildQuery($table, $params, $parts = ['values', 'conditions', 'join']);
	}
	
	/**
	 * Build SQL select query from parameters array
	 *
	 * args:	string, array, array
	 * return:	string
	 */
	private function _buildQuery($table, $params, $parts = [])
	{
		extract($this->_extractQueryParts($params, $parts));
		
		return 'SELECT ' . $values . ' FROM ' . $table . $join . $conditions . $order . $limit . $offset;
	}
	
	/**
	 * Extract SQL parts from parameters array
	 *
	 * args:	array, array
	 * return:	array
	 */
	private function _extractQueryParts($params, $parts = [])
	{
		$queryParts = [];
		
		foreach ($parts as $part)
		{
			$queryParts[$part] = (array_key_exists($part, $params)) ? $this->{'_extract' . ucfirst($part)}($params[$part]) : '';
		}
		
		$queryParts['values'] = (!empty($queryParts['values'])) ? $queryParts['values'] : '*';
		
		return $queryParts;
	}
	
	/**
	 * Extract values to select by query
	 *
	 * args:	array or string
	 * return:	array
	 */
	private function _extractValues($param = '*')
	{
		$validTypes = ['array', 'string'];
		
		if (empty($param))
		{
			return '';
		}
		
		try
		{
			if (is_array($param))
			{
				$closure = function($p) {
					if (!is_string($p))
						throw TypeHelper::getException($p, ['string']);
					
					return $p;
				};
				
				return implode(', ', array_map($closure, $param));
			}
			else if (is_string($param))
			{
				return $param;
			}
			else
			{
				throw TypeHelper::getException($param, $validTypes);
			}
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
	}
	
	/**
	 * Extract join clause to query
	 *
	 * args:	string
	 * return:	string
	 */
	private function _extractJoin(string $param)
	{
		return '';
	}
	
	/**
	 * Extract where clause to query
	 *
	 * args:	array or string
	 * return:	string
	 */
	private function _extractConditions($param)
	{
		$validTypes = ['array', 'string'];
		
		if (empty($param))
		{
			return '';
		}
		
		try
		{
			if (is_array($param))
			{
				$closure = function($p) {
					if (!is_string($p) && !is_integer($p))
						throw TypeHelper::getException($p, ['string']);
					
					return $p;
				};
				
				return ' WHERE (' . implode(' AND ', array_map($closure, $param)) . ')';
			}
			else if (is_string($param))
			{
				return ' WHERE ' . $param;
			}
			else
			{
				throw TypeHelper::getException($param, $validTypes);
			}
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
	}
	
	/**
	 * Extract order to query
	 *
	 * args:	string
	 * return:	string
	 */
	private function _extractOrder($param)
	{
		$validTypes = ['string'];
		
		if (empty($param))
		{
			return '';
		}
		
		try
		{
			if (!TypeHelper::valid($param, $validTypes))
			{
				throw TypeHelper::getException($param, $validTypes);
			}
			
			return ' ORDER BY ' . $param;
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
	}
	
	/**
	 * Extract limit clause to query
	 *
	 * args:	string or integer
	 * return:	string
	 */
	private function _extractLimit($param)
	{
		$validTypes = ['integer', 'string'];
		
		if (empty($param))
		{
			return '';
		}
		
		try
		{
			if (!TypeHelper::valid($param, $validTypes))
			{
				throw TypeHelper::getException($param, $validTypes);
			}
			
			return ' LIMIT ' . $param;
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
	}
	
	/**
	 * Extract offset to query
	 *
	 * args:	string or integer
	 * return:	string
	 */
	private function _extractOffset($param)
	{
		$validTypes = ['integer', 'string'];
		
		if (empty($param))
		{
			return '';
		}
		
		try
		{
			if (!TypeHelper::valid($param, $validTypes))
			{
				throw TypeHelper::getException($param, $validTypes);
			}
			
			return ' OFFSET ' . $param;
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
	}
	
	/**
	 * Extract offset to query
	 *
	 * args:	string, array, string
	 * return:	object
	 */
	public function find(string $table, $params = [], $class = '')
	{
		if ($this->_read($table, $params, $class))
		{
			return $this->results();
		}
	}
	
	public function findFirst(string $table, $params = [], $class = '')
	{
		if ($this->_read($table, $params, $class))
		{
			return $this->first();
		}
		return false;
	}
	
	public function insert(string $table, $fields = [])
	{
		$fieldString = '';
		$valueString = '';
		$values = [];
		
		try 
		{
			foreach ($fields as $field => $value)
			{
				if (!is_string($field) && !TypeHelper::valid($value, ['string', 'integer', 'float']))
				{
					throw TypeHelper::getException($value, $validTypes);
				}
				
				$fieldString .= '`' . $field . '`,';
				$valueString .= '?,';
				$values[] = $value;
			}
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
		
		$fieldString = rtrim($fieldString, ',');
		$valueString = rtrim($valueString, ',');
		
		$sql = 'INSERT INTO ' . $table . ' (' . $fieldString . ') ' . 
				'VALUES (' . $valueString . ')';
		
		if (!$this->_query = $this->_pdo->prepare($sql))
		{
			return false;
		}
		
		if (!empty($values))
		{
			$i = 1;
			
			foreach ($values as $param)
			{
//				if (!TypeHelper::valid($param, ['string', 'integer', 'float']))
//				{
//					throw TypeHelper::getException($param, true);
//				}

				$this->_query->bindValue($i, $param);
				$i++;
			}
		}
			
		$this->_error = ($this->_query->execute()) ? false : true;
		$this->_lastInsertId = $this->_pdo->lastinsertId();
		
		return !$this->_error;
	}
	
	public function update(string $table, int $id, array $fields = [])
	{
		$fieldString = '';
		$values = [];
		
		try 
		{
			foreach ($fields as $field => $value)
			{
				if (!is_string($field) || !TypeHelper::valid($value, ['string', 'integer', 'float']))
				{
					throw TypeHelper::getException($value, $validTypes);
				}
				
				$fieldString .= ' ' . $field . ' = ?,';
				$values[] = $value;
			}
		}
		catch (InvalidTypeException $e)
		{
			die($e->getMessage());
		}
		
		$fieldString = trim($fieldString);
		$fieldString = rtrim($fieldString, ',');
		$sql = 'UPDATE ' . $table . ' SET ' . $fieldString . ' WHERE id = ' . $id;
		
		if (!$this->query($sql, $values)->error())
		{
			return true;
		}
		return false;
	}
	
	public function delete(string $table, int $id)
	{
		$sql = 'DELETE FROM ' . $table . ' WHERE id = ' . $id;
		
		if (!$this->query($sql)->error())
		{
			return true;
		}
		return false;
	}
	
	public function deleteBy(string $table, $values)
	{
		$sql = 'DELETE FROM ' . $table . ' WHERE ';
		$params = [];
		
		if (empty($values) || !is_array($values))
		{
			return false;
		}
		
		foreach ($values as $key => $value)
		{
			$sql .= $key . ' = ? AND ';
			$params[] = $value;
		}

		$sql = rtrim($sql, ' AND ');
		$i = 1;
		$this->_query = null;
		
		if (!$this->_query = $this->_pdo->prepare($sql))
		{
			return false;
		}
		
		foreach ($params as $param)
		{
			if (!TypeHelper::valid($param, ['string', 'integer', 'float']))
			{
				throw TypeHelper::getException($param, true);
			}

			$this->_query->bindValue($i, $param);
			$i++;
		}
		
		$this->_error = ($this->_query->execute()) ? false : true;
		
		return !$this->_error;
	}
	
	public function results()
	{
		return $this->_result;
	}
	
	public function first()
	{
		return (!empty($this->_result)) ? $this->_result[0] : [];
	}
	
	public function count()
	{
		return $this->_count;
	}
	
	public function lastInsertId()
	{
		return $this->_lastInsertId;
	}
	
	public function get_columns(string $table)
	{
		return $this->query('SHOW COLUMNS FROM ' . $table)->results();
	}
	
	public function error()
	{
		return $this->_error;
	}
}