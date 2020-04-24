<?php

namespace Core\Classes;
use Core\Classes\Session;


class View
{
	protected 	$_outputBuffer,
				$_title = SITE_TITLE,
				$_layout = DEFAULT_LAYOUT,
				$_head,
				$_body;
	
	public function __construct()
	{
		
	}
	
	public function render($subPath)
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
	
	public function content($type)
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
	
	public function start($type)
	{
		if ($type === 'head' || $type === 'body')
		{
			$this->_outputBuffer = $type;
			
			ob_start();
		}
	}
	
	public function end()
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
	
	public function include($element)
	{
		$path = ROOT . DS . 'app' . DS . 'views' . DS . 'includes' . DS . $element . '.php';
		
		if (is_readable($path))
		{
			include $path;
		}
	}
	
	public function setSiteTitle($title)
	{
		$this->_title = $title;
	}
	
	public function siteTitle()
	{
		return $this->_title;
	}
}