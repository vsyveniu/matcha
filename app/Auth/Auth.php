<?php

namespace App\Auth;

class Auth {
	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function find(){
		if (isset($_SESSION['user']))
			return $this->container->user->findUserById($_SESSION['user']);
	}

	public function check(){
		return isset($_SESSION['user']);
	}

	public function attempt($username, $password){
		$user = $this->container->user->getUserByUsername($username);
		if(!$user)
			return false;

		if(password_verify($password, $user['password']) && $user['verified'] == 'Y'){
			$_SESSION['user'] = $user['id'];
			return true;
		}

		return false;
	}

	public function logout() {
		unset($_SESSION['user']);
	}
}

?>