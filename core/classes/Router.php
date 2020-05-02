<?php

declare(strict_types = 1);

namespace Core\Classes;
use Core\Classes\Sanitizers\URLSanitizer;
use Core\Classes\Controller;
use Core\Classes\URL;


class Router
{
	private static $_url;
	
	public static function route()
	{
		$url = URLSanitizer::sanitize($_SERVER['PATH_INFO']);
		$url = explode('/', trim($url, '/'));
		
		if (isset($url[0]) && !empty(ltrim($url[0], ' -')))
			$controllerName = str_replace('\?[^\/]*$', '', implode('', array_map('ucwords', explode('-', $url[0]))));
		else
			$controllerName = DEFAULT_CONTROLLER;
		
		$controller = $controllerName . 'Controller';
		
		if (isset($url[1]) && !empty($url[1]))
			$actionName = str_replace('-', '_', str_replace('/(\?[^\/]*)?/', '', $url[1]));
		else
			$actionName = DEFAULT_ACTION;
		
		$action = $actionName . '_action';
		
		$grantAccess = self::hasAccess($controllerName, $actionName);

		if (!$grantAccess)
		{
			$controllerName = ACCESS_RESTRICTED;
			$controller = $controllerName . 'Controller';
			$actionName = DEFAULT_ACTION;
			$action = $actionName . '_action';
		}
		
		$controllerWithNamespace = Controller::get($controller);
		$controller = new $controllerWithNamespace;
		
		if (!class_exists($controllerWithNamespace))
		{
			self::loadDefaultPage();
		}
		else if (!method_exists($controllerWithNamespace, $action))
		{
			self::loadErrorPage(404);
		}
		else
		{
			call_user_func_array([$controller, $action], array_slice($url, 2));
		}
	}
	
	public static function redirect($location)
	{
		echo BASE_URL . $location;
		
		if (!headers_sent())
		{
			header('Location: ' . BASE_URL . $location);
			exit;
		}
		else 
		{
			echo '<script type="text/javascript">';
			echo 'window.location.href="', BASE_URL, $location, '";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url=', BASE_URL, $location, '"/>';
			echo '</noscript>';
			exit;
		}
	}
	
	public static function hasAccess($controllerName, $actionName = 'index')
	{
		$aclFile = file_get_contents(ROOT . DS . 'app' . DS . 'acl' . DS . 'acl.json');
		$acl = json_decode($aclFile, true);
		$currentUserAcls = ['Guest'];
		$grantAccess = false;
		
		if (isset($_SESSION[SESSION_USER_ID_NAME]))
		{
			$currentUserAcls[] = 'LoggedIn';
			
			if (isset($_SESSION[SESSION_USER_ACL_NAME]))
			{
				$currentUserAcls[] = ucfirst($_SESSION[SESSION_USER_ACL_NAME]);
			}
		}
		
		foreach ($currentUserAcls as $level)
		{
			if (array_key_exists($level, $acl) && 
				array_key_exists($controllerName, $acl[$level]))
			{
				if (in_array($actionName, $acl[$level][$controllerName]) ||
				   in_array('*', $acl[$level][$controllerName]))
				{
					$grantAccess = true;
				}
			}
			
			$denied = $acl[$level]['denied'];
			
//			if (!empty($denied) && (in_array($controllerName, $denied) ||
//				(is_array($denied[$controllerName]) && 
//				 (in_array($actionName, $denied[$controllerName]) ||
//				 in_array('*', $denied[$controllerName])))))
			if (!empty($denied))
			{
				if (is_array($denied[$controllerName]) &&
				   (in_array($actionName, $denied[$controllerName]) ||
				   in_array('*', $denied[$controllerName])))
				{
					$grantAccess = false;
				}
				else if (in_array($controllerName, $denied))
				{
					$grantAccess = false;
				}
			}
		}
		
		return $grantAccess;
	}
	
	public static function getMenu($menu)
	{
		$menuArray = [];
		$menuFile = file_get_contents(ROOT . DS . 'app' . DS . 'acl' . DS . $menu . '.json');
		$acl = json_decode($menuFile, true);
		
		foreach ($acl as $key => $value)
		{
			if (is_array($value))
			{
				$sub[] = '';
				
				foreach ($value as $k => $v)
				{
					if ($k === 'separator' && !empty($sub))
					{
						$sub[$k] = '';
						
						continue;
					}
					else if ($finalValue = self::getLink($v))
					{
						$sub[$k] = $finalValue;
					}
				}
				
				if (!empty($sub))
				{
					$menuArray[$key] = $sub;
				}
			}
			else
			{
				if ($finalValue = self::getLink($value))
				{
					$menuArray[$key] = $finalValue;
				}
			}
		}
		
		return $menuArray;
	}
	
	public static function getLink($value)
	{
		if (preg_match('/https?:\/\//', $value) == 1)
		{
			return $value;
		}
		else
		{
			$urlArray = explode('/', $value);
			$controllerName = ucwords($urlArray[0]);
			$actionName = (isset($urlArray[1])) ? $urlArray[1] : '';
			
			if (self::hasAccess($controllerName, $actionName))
			{
				return BASE_URL . $value;
			}
			return false;
		}
	}
	
	private static function loadDefaultPage()
	{
		$controllerWithNamespace = Controller::get(DEFAULT_CONTROLLER . 'Controller');
		$controller = new $controllerWithNamespace;
		$action = DEFAULT_ACTION;

		$controller->$action();
	}
	
	private static function loadErrorPage($code)
	{
		$controllerWithNamespace = Controller::get(ERROR_CONTROLLER . 'Controller');
		$controller = new $controllerWithNamespace;
		$action = ERROR_ACTION;

		$controller->$action($code);
	}
}