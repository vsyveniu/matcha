<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

class PasswordController extends Controller{
	public function getChangePassword($request, $response){
		return $this->view->render($response, 'auth/password/change.twig');
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