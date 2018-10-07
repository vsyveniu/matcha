<?php

namespace App\Controllers;

use\PDO;

class HomeController extends Controller{


	public function index($request, $response){

		return $this->view->render($response, 'home.twig');
	}

}

