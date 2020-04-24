<?php

namespace Core\Abstracts;
use Core\Classes\EmailView;
use Core\Interfaces\Sendable;


abstract class Email implements Sendable
{
	public function __construct()
	{
		$this->view = new EmailView;
	}
	
	public function send()
	{
		return mail($this->data->receiver, $this->subject, 
					$this->message, $this->headers);
	}
	
	public function arrayToObject($array) 
	{
		$object = new \stdClass;
		
		foreach ($array as $k => $v) 
		{
			if (!empty($k)) 
			{
				if (is_array($v)) 
				{
					$object->{$k} = $this->arrayToObject($v); //RECURSION
				} 
				else 
				{
					$object->{$k} = $v;
				}
			}
		}
		
		return $object;
	}
}