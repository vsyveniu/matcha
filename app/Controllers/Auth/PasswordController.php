<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

class PasswordController extends Controller{
	public function getChangePassword($request, $response){
		return $this->view->render($response, 'auth/password/change.twig');
	}
	public function restorePass($request, $response)
	{	
		$params = $request->getQueryParams();
		$name = htmlspecialchars(strip_tags($params['username']));
		$hash = htmlspecialchars(strip_tags($params['hash']));
		$password = htmlspecialchars($params['password']);
		if(!preg_match('/^[a-z\d_]{2,20}$/i', $name) || ($this->container->userProfile->getHash($name) != $hash))
		{
			$this->flash->addMessage('info', "You try to make some bad things");	
			return $response->withRedirect($this->router->pathFor('auth.reinit'));
		}
		else
		{
				$res = $this->container->userProfile->writeNewPassword($name, $password);
		
				if($res)
				{		
					$this->flash->addMessage('info', 'Your password has been restored');
					return $response->withRedirect($this->router->pathFor('home'));
				}
				else
				{
					$this->flash->addMessage('info', "You try to make some bad things");	
					return $response->withRedirect($this->router->pathFor('auth.reinit'));
				}	
		}

	}

	public function postChangePassword($request, $response){
		$user = $this->auth->find();
		$validation = $this->validator->validate($request, ['oldPassword', 'password', 'confirmPassword']);

		if($validation->failed())
			return $response->withRedirect($this->router->pathFor('auth.password.change'));
		else
			$this->user->changePassword($request, $user);

		$this->flash->addMessage('info', 'Your password has been changed successfully!');	
		return $response->withRedirect($this->router->pathFor('auth.password.change'));
	}


}

?>