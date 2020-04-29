<?php


define('ROOT_DIR', 'P:/xampp/htdocs/files/Projects/Framework');

//require ROOT_DIR . 'index.php';

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__);

error_reporting(E_ALL & ~E_NOTICE);

require ROOT_DIR . DS . 'config' . DS . 'config.php';


function autoload($className)
{
	$pathArray = explode(DS, $className);
	$class = array_pop($pathArray);
	$subPath = implode(DS, array_map('strtolower', $pathArray));
	$path = ROOT_DIR . DS . $subPath . DS . $class . '.php';
	
	if (is_readable($path))
	{
		require_once $path;
	}
}


function d($content)
{
	echo '<pre>';
	
	if (is_array($content) || is_object($content))
	{
		var_dump($content);
	}
	else
	{
		echo $content;
	}
	
	echo '</pre>';
}


function dd($content)
{
	d($content);
	die;
}


spl_autoload_register('autoload');
