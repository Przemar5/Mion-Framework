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
	
	/** @test */
	public function getInstance_returns_object_self()
	{
		
	}
}