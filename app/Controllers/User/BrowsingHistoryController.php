<?php

namespace App\Controllers\User;
use App\Controllers\Controller;

Class BrowsingHistoryController extends Controller{
    public function index($request, $response) {
        return $this->view->render($response, 'user/visit.twig', ['visits' => $this->visits->getByVisitorId($_SESSION['user'])->fetchAll()]);
    }

    public function delete($request, $response) {
        $id = $_SESSION['user'];
		$this->visits->clear_history($id);

		exit();
    }

    public function load($request, $response)
    {
    	$params = $request->getParsedBody();
    	if(isset($params['method']) && $params['method'] == "load")
    	{
    		$visits = $this->visits->getByVisitorId($_SESSION['user'])->fetchAll();
    		return $response->withJson($visits);
		}
		return ;
		
    }
}

?>