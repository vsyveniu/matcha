<?php

namespace App\Middleware;

class UserProfileMiddleware extends Middleware {
	public function __invoke($request, $response, $next){
      	$this->container->view->getEnvironment()->addGlobal('profiles', $this->container->userProfile->getAllUsersProfiles());
		
		$response = $next($request, $response);
		return $response;
	}
}

?>
