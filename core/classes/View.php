<?php

declare(strict_types = 1);

namespace Core\Classes;
use Core\Classes\Session;


class View
{
	protected string $_outputBuffer;
	protected string $_title = SITE_TITLE;
	protected string $_layout = DEFAULT_LAYOUT;
	protected string $_head;
	protected string $_body;
	
	public function __construct()
	{
		
	}
	
	public function render(string $subPath): void
	{
		$subPath = implode(DS, explode('/', $subPath));
		$path = ROOT . DS . 'app' . DS . 'views' . DS . $subPath . '.php';
		$layout = ROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . $this->_layout . '.php';
		
		if (is_readable($path))
		{
			include $path;
			include $layout;
		}
	}
	
	public function content(string $type): string
	{
		if ($type === 'head')
		{
			return $this->_head;
		}
		if ($type === 'body')
		{
			return $this->_body;
		}
	}
	
	public function start(string $type): void
	{
		if ($type === 'head' || $type === 'body')
		{
			$this->_outputBuffer = $type;
			
			ob_start();
		}
	}
	
	public function end(): void
	{
		if ($this->_outputBuffer === 'head')
		{
			$this->_head = ob_get_clean();
		}
		else if ($this->_outputBuffer === 'body')
		{
			$this->_body = ob_get_clean();
		}
	}
	
	public function include(string $element): void
	{
		$path = ROOT . DS . 'app' . DS . 'views' . DS . 'includes' . DS . $element . '.php';
		
		if (is_readable($path))
		{
			include $path;
		}
	}
	
	public function setSiteTitle(string $title): void
	{
		$this->_title = $title;
	}
	
	public function siteTitle(): string
	{
		return $this->_title;
	}
}