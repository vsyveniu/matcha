<?php

namespace App\Middleware;

class IsFilledMiddleware extends Middleware {
	public function __invoke($request, $response, $next){
		if(!$this->container->auth->is_filled()){
			return $response->withRedirect($this->container->router->pathFor('user.profile'));
		}

		$response = $next($request, $response);
		return $response;
	}
}


?>