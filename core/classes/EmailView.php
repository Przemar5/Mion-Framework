<?php

declare(strict_types = 1);

namespace Core\Classes;
use Core\Classes\View;


class EmailView extends View
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_layout = 'emails' . DS . DEFAULT_EMAIL_LAYOUT;
	}
}