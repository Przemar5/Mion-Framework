<?php

namespace Core\Classes\Emails;
use Core\Abstracts\Email;
use Core\Classes\Auth;


class ActivateAccountEmail extends Email
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function prepare($data)
	{
		$this->data = $this->arrayToObject($data);
		$this->subject = 'Registration Confirm';
		$this->_getHeaders();
		$this->_getMessage();
	}
	
	private function _getHeaders()
	{
		$this->headers = "From: " . SITE_TITLE . "\r\n";
		$this->headers .= "Reply-To: noreply@example.com\r\n";
		$this->headers .= "CC: " . MAIN_EMAIL_ADDRESS . "\r\n";
		$this->headers .= "MIME-Version: 1.0\r\n";
		$this->headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	}
	
	private function _getMessage()
	{
		$this->view->data = $this->data->message ?? null;
		
		ob_start();
		$this->view->render('emails/activate_account_email');
		$this->message = ob_get_clean();
	}
}