<?php

declare(strict_types = 1);

namespace App\Models\TestModel;
use Core\Classes\Model2;


class TestModel extends Model2
{
	public function __construct()
	{
		parent::__construct('user');
	}
}