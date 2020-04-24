<?php

namespace Core\Abstracts;
use Core\Interfaces\SanitizeInterface;


abstract class Sanitizer implements SanitizeInterface
{
	abstract public static function sanitize($value);
}