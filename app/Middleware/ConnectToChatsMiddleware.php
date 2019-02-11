<?php

namespace App\Middleware;

use App\Pusher\CreateConnection;

class ValidationErrorsMiddleware extends Middleware {

	public function __invoke($request, $response, $next){
		$chats = $this->container->chats->find_all_chats($_SESSION['user']);
		$trigger = 'message';

		if($chats){
			foreach($chats as $chat){
				$chanel = md5(md5($chat['user_id']).md5($chat['user1_id']));
				$pusher = new CreateConnection();
				$pusher->newConnection($chanel, $trigger);
			}
		}
		
		$response = $next($request, $response);
		return $response;
	}
}

?>