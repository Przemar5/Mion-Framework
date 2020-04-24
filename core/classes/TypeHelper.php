<?php

namespace Core\Classes;


class TypeHelper
{
	public static function valid($value, $types = ['string', 'integer', 'float'])
	{
		$result = false;
		
		foreach ($types as $type)
		{
			$func = 'is_' . $type;
			$result = $result || $func($value);
		}
		
		return $result;
	}
	
	/**
	 * Function for returning prepared InvalidArguentException with filename and line number in it's message.
	 *
	 * args:	argument of any type
	 * return:	InvalidTypeException
	 */
	public static function getException($arg, $types = ['string', 'integer', 'float'])
	{
		$backtrace = debug_backtrace()[0];
		
		$types = ($types) ? implode(', ', array_map(function($v) {	return "'" . $v . "'";	}, $types)) : '';
		$fileName = $backtrace['file'];
		$lineInfo = $backtrace['line'];
		$message = '<strong>' . $fileName . '</strong>. Line: <strong>' . $lineInfo . '</strong>: Given argument is invalid type (' . gettype($arg) . '). Valid types are: ' . $types . '.';
		
		return new Exceptions\InvalidTypeException($message);
	}
	
	
}