<?php

namespace App\Controllers\User;

use App\Controllers\Controller;

class NotificationController extends Controller{
	public function notifications($request, $response) {
		return $this->view->render($response, 'user/notofications.twig', ['notifications' => $this->notification->getNotifications($_SESSION['user'])->fetchAll()]);
	}




	public function postNotifications($request, $response)
	{
		$params = $request->getParsedBody();
    	if(isset($params['method']) && $params['method'] == "load")
    	{
		$notes = $this->notification->getNotifications($_SESSION['user'])->fetchAll();
		}	
		return $response->withJson($notes);
	}

	

	public function viewed($request, $response){
		$param = $request->getParsedBody();
		$id = $param['id'];

		$this->notification->viewed($id);
		return $response->withJson($id);

	}

	public function delete($request, $response){
		$param = $request->getParsedBody();
		$id = $param['id'];
		$uid = $_SESSION['user'];
		if($id !== 'all')
			$this->notification->delete($id);
		else
			$this->notification->delete_all($uid);
		return $response->withJson($id);
	}

	public function count($request, $response){
		return $response->withJson($this->notification->count($_SESSION['user']));
	}
}

?>