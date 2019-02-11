<?php

namespace App\Controllers\User;

use App\Controllers\Controller;

class TagsController extends Controller{
	public function save_tag($request, $response) {
		$post = $request->getParsedBody();
		$this->tag->save($post['tag'], $_SESSION['user']);

 		exit();
	}

	public function get_tags($request, $response) {
		$tags = $this->tag->get_tags($_SESSION['user']);
	
 		exit();
	}
}

?>