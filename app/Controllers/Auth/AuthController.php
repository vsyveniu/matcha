<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

class AuthController extends Controller{
	public function getSignOut($request, $response){
		$this->auth->logout();
		
		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignIn($request, $response){
		return $this->view->render($response, 'auth/signin.twig');
	}

	public function postSignIn($request, $response){
		$auth = $this->auth->attempt(
			$request->getParam('username'),
			$request->getParam('password')
		);

		if(!$auth){
			$this->flash->addMessage('error', 'Could not sign you in with those details.');
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}		
		else			
			return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignUp($request, $response){
		return $this->view->render($response, 'auth/signup.twig');
	}

	public function postSignUp($request, $response){
		$validation = $this->validator->validate($request, ['email', 'username', 'firstName', 'lastName', 'password', 'confirmPassword']);

		if($validation->failed())
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		else{
			$this->user->create($request);
			$this->sendEmail->verification($request);
		}
		
		$this->flash->addMessage('info', 'You have been signed up! Please confirm youre email');	
		return $response->withRedirect($this->router->pathFor('home'));
	}
}

?>