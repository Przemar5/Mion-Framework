<?php

declare(strict_types = 1);

namespace App\Models;
use Core\Classes\Hash;
use Core\Classes\Model;
use Core\Classes\Session;
use Core\Classes\Validation;


class UserModel extends Model
{
	public $id, $username, $email, $first_name, $last_name, 
			$password, $rePassword, $verified, $deleted;
	
	
	public function __construct()
	{
		parent::__construct('user');
		
		$this->_tableProperties = ['id', 'username', 'email', 'first_name', 'last_name', 'password'];
	}
	
	public function findUser(string $username)
	{
		$data = [
			'bind' => [$username],
			'conditions' => 'username = ?'
		];
		
		return $this->findFirst($data);
	}
	
	public function findEmail(string $email)
	{
		$data = [
			'bind' => [$email],
			'conditions' => 'email = ?'
		];
		
		return $this->findFirst($data);
	}
	
	public static function currentUser()
	{
		if (!Session::exists(SESSION_USER_ID_NAME))
			return false;
		
		$id = Session::get(SESSION_USER_ID_NAME);
		
		if (empty($id) || !is_numeric($id))
			return false;
		
		$data = [
			'bind' => [$id],
			'conditions' => 'id = ?'
		];
		
		return (Model::load('user'))->findFirst($data);
	}
	
	public static function getUser($id)
	{
		$data = [
			'bind' => [$id],
			'conditions' => 'id = ?'
		];
		
		return (Model::load('user'))->findFirst($data);
	}
	
	public function setSession(): void
	{
		Session::regenerateId();
		Session::set(SESSION_USER_ID_NAME, $this->id);
		Session::set(SESSION_USER_ACL_NAME, $this->acl);
	}
	
	public function changePassword(string $password, string $rePassword)
	{
		$this->loadValidationRules();
		$this->_validationRules['password']['match'] = [
			'args' => [$rePassword], 
			'msg' => 'Both passwords must match each other.'
		];
		
		if ($this->validate(['password']))
		{
			$data = [
				'password' => Hash::make($password)
			];

			return $this->update($this->id, $data);
		}
		else
		{
			return false;
		}
	}
	
	public function register(): bool
	{
		$this->loadValidationRules();
		$this->validationRules['password']['match'] = [
			'args' => [$this->rePassword], 
			'msg' => 'Both passwords must match each other.'
		];
		
		if ($this->validate())
		{
//			die('ok');
			
			$this->password = Hash::make($this->password);
			
			if ($this->save())
			{
				$this->id = $this->_db->lastInsertId();
				
				return true;
			}
		}
		else
		{
//			die('not');
			return false;
		}
	}
	
	public function errors(): ?array
	{
		return $this->_errors;
	}
}