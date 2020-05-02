<?php

declare(strict_types = 1);

namespace Core\Classes;
use Core\Classes\View;
use Core\Classes\Model;
use Core\Classes\Session;


class Controller extends Application
{
	private $_controller;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->view = new View;
	}
	
	public static function get(string $name): string
	{
		$path = CONTROLLERS_NAMESPACE . $name;
		
		if (class_exists($path))
		{
			return $path;
		}
		
		$alternativePath = AUTH_CONTROLLERS_NAMESPACE . $name;
		
		if (class_exists($alternativePath))
		{
			return $alternativePath;
		}
	}
	
	public function loadModel(string $table): void
	{
		$this->{$table . 'Model'} = Model::load($table);
	}
}