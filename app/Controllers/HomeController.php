<?php

namespace App\Controllers;

class HomeController extends Controller{
	public function index($request, $response){
		
		return $this->view->render($response, 'home.twig', [
				'user' => $_SESSION['user']]);
	}
}


