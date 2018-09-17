<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

class ConfirmEmailController extends Controller{
	public function getConfirm($request, $response, $args){
		$getParams = $request->getQueryParams();

		if(isset($getParams['username']) && isset($getParams['hash'])){
			$user = $this->user->getUserByUsername($getParams['username']);

			if($user && $getParams['hash'] == md5(md5($getParams['username']))){
				$this->user->emailVerifide($user);
				$this->flash->addMessage('info', 'Your Email Has Been Confirmed!');
				return $response->withRedirect($this->router->pathFor('auth.signin'));
			}	
		}

		$this->flash->addMessage('error', 'Bad verification ULR');
		return $response->withRedirect($this->router->pathFor('home'));	
	}
}

?>