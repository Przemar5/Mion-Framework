<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Classes;
use Core\Classes\Database2;
use \PDO;
use \PDOException;
use Core\Exceptions\{ClassNotFoundException, InvalidTypeException};
use Core\Classes\TypeHelper;
use PHPUnit\Framework\TestCase;


require __DIR__ . '/../../index.php';

//echo __DIR__ . '/../../index.php';

class Database2Test extends TestCase
{
	public $db;
	public $data;
	
	public function setUp(): void
	{
		$this->db = Database2::getInstance();

		$this->data = [
			'buildSelectQuery' => [
				'assertEquals' => [
					[
						'table' => 'friend',
						'input_array' => [
							'values' => [],
							'bind' => [1],
							'conditions' => 'id = ?'
						],
						'output' => 'SELECT * FROM friend WHERE id = ?'
					],
					[
						'table' => 'friend',
						'input_array' => [],
						'output' => 'SELECT * FROM friend'
					],
					[
						'table' => 'friend',
						'input_array' => [
							'bind' => [1],
						],
						'output' => 'SELECT * FROM friend'
					],
					[
						'table' => 'friend',
						'input_array' => [
							'values' => '',
							'bind' => [1],
							'conditions' => 'id = ?'
						],
						'output' => 'SELECT * FROM friend WHERE id = ?'
					],
					[
						'table' => 'friend',
						'input_array' => [
							'values' => ['email', 'username'],
							'bind' => [1],
							'conditions' => 'id = ?'
						],
						'output' => 'SELECT email, username FROM friend WHERE id = ?'
					],
					[
						'table' => 'friends',
						'input_array' => [
							'values' => ['email', 'username'],
							'bind' => [1],
							'conditions' => 'id = ?',
							'limit' => 2,
							'offset' => 1
						],
						'output' => 'SELECT email, username FROM friends WHERE id = ? LIMIT 2 OFFSET 1'
					],
					[
						'table' => 'friend',
						'input_array' => [
							'values' => ['email, username'],
							'bind' => [1, 'Adam'],
							'conditions' => 'id = ? AND name = ?',
							'offset' => 1.0,
							'limit' => 2.0
						],
						'output' => 'SELECT email, username FROM friend WHERE id = ? AND name = ? LIMIT 2 OFFSET 1'
					],
					[
						'table' => 'friend',
						'input_array' => [
							'values' => ['  email,  username  '],
							'bind' => [1, 'Adam'],
							'conditions' => '( id = ? AND name = ? )'
						],
						'output' => 'SELECT email,  username FROM friend WHERE ( id = ? AND name = ? )'
					]
				],
				'expectException' => [
					[
						'table' => '',
						'input_array' => [],
						'output' => 'Table name is required.'
					],
					[
						'table' => 'my friend',
						'input_array' => [],
						'output' => 'Table name cannot contain white characters.'
					],
					[
						'table' => '\das;das',
						'input_array' => [],
						'output' => 'Table name contains forbidden characters.'
					]
				]
			],
			'buildInsertQuery' => [
				[
					'table' => 'friend',
					'input_array' => [
						'username' => 'Adam',
						'email' => 'adam@mail.com'
					],
					'output' => 'INSERT INTO friend (username, email) VALUES (?, ?)'
				],
				[
					'table' => 'friend',
					'input_array' => [
						'username' => '  Adam  '
					],
					'output' => 'INSERT INTO friend (username) VALUES (?)'
				]
			],
			'buildUpdateQuery' => [
				[
					'table' => 'friend',
					'main_key' => 'id',
					'input_array' => [
						'username' => 'Adam',
						'email' => 'adam@mail.com'
					],
					'output' => 'UPDATE friend SET username = ?, email = ? WHERE id = ?'
				],
				[
					'table' => 'friend',
					'main_key' => 'id',
					'input_array' => [
						'username' => 'Adam'
					],
					'output' => 'UPDATE friend SET username = ? WHERE id = ?'
				]
			]
		];
	}
	
	/** @test */
	public function can_get_database_instance()
	{
		$this->assertInstanceOf(Database2::class, $this->db);
	}
	
	// buildSelectQuery

	/** @test */
	// public function buildSelectQuery_returns_correct_output()
	// {
	// 	$data = $this->data['buildSelectQuery']['assertEquals'];
	// 	$length = count($data);

	// 	for ($i = 0; $i < $length; $i++)
	// 	{
	// 		$query = $this->db->buildSelectQuery($data[$i]['table'], $data[$i]['input_array']);

	// 		$this->assertEquals($query, $data[$i]['output']);
	// 	}
	// }

	/** @test */
	// public function buildSelectQuery_throws_exceptions()
	// {
	// 	$data = $this->data['buildSelectQuery']['expectException'];
	// 	$length = count($data);

	// 	for ($i = 0; $i < $length; $i++)
	// 	{
	// 		$this->expectException(\Exception::class);
	// 		// $this->expectExceptionMessage($data[$i]['output']);

	// 		try 
	// 		{
	// 			$query = $this->db->buildSelectQuery($data[$i]['table'] ?? 'a', $data[$i]['input_array']);
	// 		}
	// 		catch (\Exception $e)
	// 		{
	// 			$e->getMessage();
	// 		}
	// 		finally 
	// 		{
	// 			// continue;
	// 		}
	// 	}
	// }


	// _getTableQueryPart

	/** @test */
	public function buildSelectQuery_trims_table_name_when_starts_or_ends_with_spaces()
	{
		$data = [
			'table' => '  table    ',
			'input_array' => [],
			'output' => 'SELECT * FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, 'SELECT * FROM table');
	}

	/** @test */
	public function buildSelectQuery_throws_exception_when_passing_table_name_containing_spaces()
	{
		$data = [
			'table' => 'table name',
			'input_array' => [],
			'output' => 'Table name cannot contain white characters.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);
	}

	/** @test */
	public function buildSelectQuery_throws_exception_when_passing_table_name_containing_forbidden_characters()
	{
		$data = [
			'table' => 'table.name',
			'input_array' => [],
			'output' => 'Table name contains forbidden characters.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);
	}


	// _getValuesQueryPart

	/** @test */
	public function buildSelectQuery_ignores_empty_values()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => null
			],
			'output' => 'SELECT * FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_accepts_string_as_value()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => 'value'
			],
			'output' => 'SELECT value FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_trims_string_value()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => '  id,  name   '
			],
			'output' => 'SELECT id,  name FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_accepts_array_as_value()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['id', 'name']
			],
			'output' => 'SELECT id, name FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_trims_all_values_passed_in_array()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['  id  ', '   name  ']
			],
			'output' => 'SELECT id, name FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_does_not_accept_nonstring_values_in_array()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['id', 1]
			],
			'output' => "All values in array must be type 'string'."
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);
	}

	/** @test */
	public function buildSelectQuery_ignores_empty_strings_passed_in_values_array()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['id', '', 'name']
			],
			'output' => 'SELECT id, name FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_sets_values_to_all_when_result_of_processing_values_is_empty()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => [' ']
			],
			'output' => 'SELECT * FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_ignores_white_characters_strings_passed_in_values_array()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['id', '  ', 'name']
			],
			'output' => 'SELECT id, name FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_throws_exception_when_passing_nonstring_and_nonarray_values()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => 1
			],
			'output' => "Values must be type 'string' or 'array', not 'integer'."
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);
	}

	/** @test */
	public function buildSelectQuery_accepts_only_valid_value_property_names()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['table#2*ok']
			],
			'output' => 'Some of given values contains forbidden characters.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);
	}


	// _getConditionsQueryPart

	/** @test */
	public function buildSelectQuery_accepts_conditions_as_string()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['name'],
				'conditions' => 'id = ?'
			],
			'output' => 'SELECT name FROM table WHERE id = ?'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_ignores_conditions_passed_as_null()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['name'],
				'conditions' => null
			],
			'output' => 'SELECT name FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_ignores_empty_conditions_string()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['name'],
				'conditions' => ''
			],
			'output' => 'SELECT name FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_trims_conditions_string()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'values' => ['name'],
				'conditions' => '  id = ?  '
			],
			'output' => 'SELECT name FROM table WHERE id = ?'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_ignores_conditions_string_if_empty_after_trim()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'conditions' => '   '
			],
			'output' => 'SELECT * FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}


	// _getLimitQueryPart

	/** @test */
	public function buildSelectQuery_ignores_limit_passed_as_null()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'limit' => null
			],
			'output' => 'SELECT * FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_accepts_limit_passed_as_integer()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'limit' => 1
			],
			'output' => 'SELECT * FROM table LIMIT 1'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_accepts_limit_value_0()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'limit' => 0
			],
			'output' => 'SELECT * FROM table LIMIT 0'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_throws_exception_when_passing_negative_limit_value()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'limit' => -1
			],
			'output' => 'Limit must be equal or greater than 0.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);
	}


	// _getOffsetQueryPart

	/** @test */
	public function buildSelectQuery_ignores_offset_passed_as_null()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'offset' => null
			],
			'output' => 'SELECT * FROM table'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_accepts_offset_passed_as_integer()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'offset' => 1
			],
			'output' => 'SELECT * FROM table OFFSET 1'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_accepts_offset_value_0()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'offset' => 0
			],
			'output' => 'SELECT * FROM table OFFSET 0'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildSelectQuery_throws_exception_when_passing_negative_offset_value()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'offset' => -1
			],
			'output' => 'Offset must be equal or greater than 0.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);
	}

	/** @test */
	public function buildSelectQuery_generates_query_with_parts_in_correct_order()
	{
		$data = [
			'table' => 'my_table',
			'input_array' => [
				'values' => ['my_table.email', 'my_table.age', 'my_table.departament'],
				'offset' => 1,
				'limit' => 2,
				'conditions' => 'my_table.id = ? OR my_table.name = ?'
			],
			'output' => 'SELECT my_table.email, my_table.age, my_table.departament FROM my_table ' . 
						'WHERE my_table.id = ? OR my_table.name = ? LIMIT 2 OFFSET 1'
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function BuildSelectQuery_accepts_conditions_array_for_creating_complex_query()
	{
		$data = [
			'table' => 'friend',
			'input_array' => [
				'conditions' => [
					'id' => [
						'friend_coworker' => 'id'
					]
				]
			]
		];

		$query = $this->db->buildSelectQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}



	// buildInsertQuery

	/** @test */
	public function buildInsertQuery_throws_exception_when_passing_empty_array()
	{
		$data = [
			'table' => 'table',
			'input_array' => [],
			'output' => 'You must specify columns to insert data in.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildInsertQuery($data['table'], array_keys($data['input_array']));
	}

	/** @test */
	public function buildInsertQuery_throws_exception_when_passing_array_of_empty_string_field_names()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'' => 'Adam',
				'' => 'Bob'
			],
			'output' => 'You must specify columns to insert data in.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildInsertQuery($data['table'], array_keys($data['input_array']));
	}

	/** @test */
	public function buildInsertQuery_trims_field_names()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'  name  ' => 'Adam',
				'  email  ' => 'adam@mail.com'
			],
			'output' => 'INSERT INTO table (name, email) VALUES (?, ?)'
		];

		$query = $this->db->buildInsertQuery($data['table'], array_keys($data['input_array']));

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildInsertQuery_throws_exception_when_all_field_names_are_empty_after_trim()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'   ' => 'Adam',
				'  ' => 'Bob'
			],
			'output' => 'You must specify columns to insert data in.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildInsertQuery($data['table'], array_keys($data['input_array']));
	}

	/** @test */
	public function buildInsertQuery_ignores_field_names_that_are_empty_after_trim()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'   ' => 'Adam',
				'name' => 'Bob',
				'   ' => 'Cindy'
			],
			'output' => 'INSERT INTO table (name) VALUES (?)'
		];

		$query = $this->db->buildInsertQuery($data['table'], array_keys($data['input_array']));

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildInsertQuery_throws_exception_when_some_field_name_contains_forbidden_characters()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'@name' => 'Adam',
				'email' => 'adam@mail.com'
			],
			'output' => "Some column name isn't valid."
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildInsertQuery($data['table'], array_keys($data['input_array']));
	}

	/** @test */
	public function buildInsertQuery_returns_correct_output()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'name' => 'Adam',
				'email' => 'adam@mail.com'
			],
			'output' => 'INSERT INTO table (name, email) VALUES (?, ?)'
		];
			
		$query = $this->db->buildInsertQuery($data['table'], array_keys($data['input_array']));

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildInsertQuery_throws_exception_when_passing_non_string_field_names()
	{
		$data = [
			'table' => 'table',
			'input_array' => [
				'Adam',
				'adam@mail.com'
			],
			'output' => "Some column name is type 'integer', but all keys must be type 'string'."
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildInsertQuery($data['table'], array_keys($data['input_array']));
	}


	// buildUpdateQuery

	/** @test */
	public function buildUpdateQuery_throws_exception_when_passing_empty_data_array()
	{
		$data = [
			'table' => 'my_table',
			'data' => [],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => 'You must specify columns to update data.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);
	}

	/** @test */
	public function buildUpdateQuery_trims_data_field_names()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'  name  ' => 'Adam',
				'  email  ' => 'adam@mail.com'
			],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => 'UPDATE my_table SET name = ?, email = ? WHERE id = ?'
		];

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildUpdateQuery_ignores_empty_field_names()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'' => 'Adam',
				'  email  ' => 'adam@mail.com'
			],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => 'UPDATE my_table SET email = ? WHERE id = ?'
		];

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildUpdateQuery_ignore_field_names_they_are_empty_after_trim()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'   ' => 'Adam',
				'  email  ' => 'adam@mail.com'
			],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => 'UPDATE my_table SET email = ? WHERE id = ?'
		];

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildUpdateQuery_throws_exception_when_passing_only_empty_field_names()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'' => 'Adam',
				'' => 'adam@mail.com'
			],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => 'You must specify columns to update data.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);
	}

	/** @test */
	public function buildUpdateQuery_throws_exception_when_all_field_names_are_empty_after_trim()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'   ' => 'Adam',
				'   ' => 'adam@mail.com'
			],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => 'You must specify columns to update data.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);
	}

	/** @test */
	public function buildUpdateQuery_throws_exception_when_some_field_name_is_nonstring()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				1 => 'Adam',
				1.2 => 'adam@mail.com'
			],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => "Some column name is type 'integer', but all keys must be type 'string'."
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);
	}

	/** @test */
	public function buildUpdateQuery_throws_exception_when_some_field_name_is_not_valid()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'@name' => 'Adam',
				'email' => 'adam@mail.com'
			],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => "Some column name isn't valid."
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);
	}

	/** @test */
	public function buildUpdateQuery_returns_correct_output()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'name' => 'Adam', 
				'email' => 'adam@mail.com'
			],
			'additional_data' => [
				'conditions' => 'id = ?'
			],
			'output' => 'UPDATE my_table SET name = ?, email = ? WHERE id = ?'
		];

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildUpdateQuery_requires_additional_data_to_specify_rows()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'name' => 'Adam',
				'email' => 'adam@mail.com'
			],
			'additional_data' => [],
			'output' => 'You must specify which rows you want to modify.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);
	}

	/** @test */
	public function buildUpdateQuery_result_query_includes_limit_of_rows_to_modify()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'name' => 'Adam',
				'email' => 'adam@mail.com'
			],
			'additional_data' => [
				'limit' => 1
			],
			'output' => 'UPDATE my_table SET name = ?, email = ? LIMIT 1'
		];

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildUpdateQuery_result_query_includes_offset_of_rows_to_modify()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'name' => 'Adam',
				'email' => 'adam@mail.com'
			],
			'additional_data' => [
				'offset' => 1
			],
			'output' => 'UPDATE my_table SET name = ?, email = ? OFFSET 1'
		];

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);

		$this->assertEquals($query, $data['output']);
	}

	/** @test */
	public function buildUpdateQuery_generates_query_with_parts_in_correct_order()
	{
		$data = [
			'table' => 'my_table',
			'data' => [
				'my_table.email' => 'adam@mail.com', 
				'my_table.age' => 20, 
				'my_table.departament' => 'finance'
			],
			'additional_data' => [
				'offset' => 1,
				'limit' => 2,
				'conditions' => 'my_table.id = ? OR my_table.name = ?'
			],
			'output' => 'UPDATE my_table SET my_table.email = ?, my_table.age = ?, my_table.departament = ? ' . 
						'WHERE my_table.id = ? OR my_table.name = ? LIMIT 2 OFFSET 1'
		];

		$query = $this->db->buildUpdateQuery($data['table'], array_keys($data['data']), $data['additional_data']);

		$this->assertEquals($query, $data['output']);
	}


	// buildDeleteQuery

	/** @test */
	public function buildDeleteQuery_throws_exception_when_not_passing_conditions()
	{
		$data = [
			'table' => 'my_table',
			'input_array' => [],
			'output' => 'You must specify conditions to delete database record.'
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($data['output']);

		$query = $this->db->buildDeleteQuery($data['table'], $data['input_array']);
	}

	/** @test */
	public function buildDeleteQuery_generates_query_with_parts_in_correct_order()
	{
		$data = [
			'table' => 'my_table',
			'input_array' => [
				'offset' => 2,
				'limit' => 3,
				'conditions' => 'my_table.id > 10'
			],
			'output' => 'DELETE FROM my_table WHERE my_table.id > 10 LIMIT 3 OFFSET 2'
		];

		$query = $this->db->buildDeleteQuery($data['table'], $data['input_array']);

		$this->assertEquals($query, $data['output']);
	}


	// select

	/** @test */

}