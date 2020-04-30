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
	
	public function setUp(): void
	{
		$this->db = Database2::getInstance();
	}
	
	/** @test */
	public function can_get_database_instance()
	{
		$this->assertInstanceOf(Database2::class, $this->db);
	}
	
	// buildSelectQuery

	/** @test */
	public function buildSelectQuery_returns_valid_query()
	{
		// #1
		$data = [
			'values' => ['email', 'username'],
			'bind' => [1],
			'conditions' => 'id = ?'
		];
		$query = $this->db->buildSelectQuery('friend', $data);

		$this->assertEquals($query, 'SELECT email, username FROM friend WHERE id = ?');


		// #2
		$data = [
			'values' => ['email', 'username'],
			'bind' => [1],
			'conditions' => 'id = ?',
			'limit' => 2
		];
		$query = $this->db->buildSelectQuery('friend', $data);

		$this->assertEquals($query, 'SELECT email, username FROM friend WHERE id = ? LIMIT 2');


		// #3
		$data = [
			'values' => ['email', 'username'],
			'bind' => [1],
			'conditions' => 'id = ?',
			'offset' => 1
		];
		$query = $this->db->buildSelectQuery('friend', $data);

		$this->assertEquals($query, 'SELECT email, username FROM friend WHERE id = ? OFFSET 1');


		// #3
		$data = [
			'values' => ['email', 'username'],
			'bind' => [1],
			'conditions' => 'id = ?',
			'offset' => -1
		];
		$query = $this->db->buildSelectQuery('friend', $data);

		$this->expectOutputString('Offset must be equal or greater than 0.');
	}
}