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
	public function reinit($request, $response){
		return $this->view->render($response, 'auth/reinit.twig');
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

	public function postreinit($request, $response)
	{

		$params = $request->getParsedbody();
		$name = htmlspecialchars($params['username']);
		$mail = htmlspecialchars($params['email']);
		$password = htmlspecialchars($params['newPassword']);
		if(!preg_match('/^[a-zA-Z0-9]{1,20}$/', $name) || !preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=\S+$).{6,}$/', $password))
		{
			$this->flash->addMessage('info', "I don't even want to know, what did you enter to get this place");	
			return $response->withRedirect($this->router->pathFor('auth.reinit'));
		
		}
		
		if($this->container->userProfile->checklogin_mail($name, $mail))
		{
			$this->sendEmail->reinit($name, $mail, $password);
			$this->flash->addMessage('info', 'Check your email for restoring link');	
			return $response->withRedirect($this->router->pathFor('home'));
		}
		else
		{
				
			$this->flash->addMessage('info', 'You have entered a wrong data');	
			return $response->withRedirect($this->router->pathFor('auth.reinit'));
		}
		

		
	}

	public function isAuth($request, $response) {
		//return $response->withJson($this->container->auth->check());
		if($this->container->auth->check() && $this->container->auth->is_filled())
			return $response->withJson(true);
		
		return $response->withJson(false);
	}
}
?>