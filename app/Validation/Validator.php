<?php

namespace App\Validation;

class Validator {
	public $errors = array();
	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function validate($request, $fields){
		foreach($fields as $field){
			$this->whitespace(htmlspecialchars($request->getParam($field)), $field);
			$this->$field($request);
		}

		$_SESSION['errors'] = $this->errors;

		return $this;
	}

	private function whitespace($str, $name){
		if(preg_match('/\s/',$str)){
			$this->errors[$name] = ucfirst($name).' must not contain whitespace';
		}	
	}

	private function username($request){
		if($this->container->user->getUserByUsername(htmlspecialchars($request->getParam('username'))))
    		$this->errors['username'] = "This username allready in use";
    	else if (!preg_match('/^[a-z\d_]{2,20}$/i', htmlspecialchars($request->getParam('username'))))
    		$this->errors['username'] = 'Bad username';
	}

	private function email($request){
		if($this->container->user->getUserByEmail(htmlspecialchars($request->getParam('email'))))
			$this->errors['email'] = "This E-mail allready in use";
		else if (!filter_var(htmlspecialchars($request->getParam('email')), FILTER_VALIDATE_EMAIL)) 
    		$this->errors['email'] = "Bad E-mail";
	}

	private function password($request){
		if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $request->getParam('password')))
    		$this->errors['password'] = 'The password does not meet the requirements!';
	}

	private function firstName($request){
		if (strlen(htmlspecialchars($request->getParam('firstName'))) == 0) 
	    	$this->errors['firstName'] = "Field 'First name' can not be empty";
	}

	private function lastName($request){
    	if (strlen(htmlspecialchars($request->getParam('lastName'))) == 0) 
    		$this->errors['lastName'] = "Field 'Last name' can not be empty";
	}

	private function confirmPassword($request){
		if(htmlspecialchars($request->getParam('password')) !== htmlspecialchars($request->getParam('confirmPassword')))
    		$this->errors['confirmPassword'] = 'Passwords do not match!';
	}

	private function oldPassword($request){
		$user = $this->container->auth->find();

		if(!password_verify($request->getParam('oldPassword'), $user['password']))
			$this->errors['oldPassword'] = 'Wrong password!';
	}

	public function failed(){
		return !empty($this->errors);
	}
}

?>