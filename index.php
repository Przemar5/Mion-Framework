<?php


use Core\Classes\Application;
use Core\Classes\Auth;
use Core\Classes\Cookie;
use Core\Classes\CustomError;
use Core\Classes\Router;
use Core\Classes\Session;
use Core\Classes\URL;
use Core\Classes\Database;
use Core\Classes\Database2;
use Core\Classes\Model2;


require_once 'config/config.php';

// Setting server variables
if (DEBUG)
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}
else
{
	error_reporting(-1);
	ini_set('display_errors', 0);
}

// Errors logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors.log');

// For security concerns, to prevent out-of-project directory traversing
ini_set('open_basedir', __DIR__);

date_default_timezone_set('UTC');

// To avoid hacking by using strange character sets in forms
ini_set('default_charset', 'UTF-8');

// To prevent server going out of memory
ini_set('memory_limit', '128M');

// To prevent from uploading too big files via POST method
ini_set('post_max_size', '128M');

// Line below is optional, if you don't want to use these
// functions it's better to disable them due to security concerns
ini_set('disable_functions', 'system,exec');


define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__);


function autoload($className)
{
	$pathArray = explode(DS, $className);
	$class = array_pop($pathArray);
	$subPath = implode(DS, array_map('strtolower', $pathArray));
	$path = ROOT . DS . $subPath . DS . $class . '.php';
	
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

// There wasn't any output before, so session could start here
Session::start();

$model = new Model2('users');

dd($model);

die;
if (!Session::exists(SESSION_USER_ID_NAME) && 
	Cookie::exists(COOKIE_REMEMBER_ME_NAME))
	Auth::getRememberedUser();

Router::route();