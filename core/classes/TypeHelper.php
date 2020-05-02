<?php

declare(strict_types = 1);

namespace Core\Classes;


class TypeHelper
{
	public static function valid($value, array $types = ['string', 'integer', 'float']): bool
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
	public static function getException($arg, array $types = ['string', 'integer', 'float']): Exceptions\InvalidTypeException
	{
		$backtrace = debug_backtrace()[0];
		
		$types = ($types) ? implode(', ', array_map(function($v) {	return "'" . $v . "'";	}, $types)) : '';
		$fileName = $backtrace['file'];
		$lineInfo = $backtrace['line'];
		$message = '<strong>' . $fileName . '</strong>. Line: <strong>' . $lineInfo . '</strong>: Given argument is invalid type (' . gettype($arg) . '). Valid types are: ' . $types . '.';
		
		return new Exceptions\InvalidTypeException($message);
	}
	
	
}