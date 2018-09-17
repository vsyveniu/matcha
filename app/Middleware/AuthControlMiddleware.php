<?php

namespace App\Middleware;

class AuthControlMiddleware extends Middleware {
	public function __invoke($request, $response, $next){	
		if(!$this->container->auth->check()){
			$this->container->flash->addMessage('error', 'Please sign in before doing this.');
			return $response->withRedirect($this->container->router->pathFor('auth.signin'));
		}

		$response = $next($request, $response);
		return $response;
	}
}

?>