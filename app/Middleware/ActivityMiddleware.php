<?php

namespace App\Middleware;

class ActivityMiddleware extends Middleware {
	public function __invoke($request, $response, $next){	
		$this->container->user->updateActivity();

		$response = $next($request, $response);
		return $response;
	}
}

?>