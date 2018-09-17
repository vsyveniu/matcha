<?php

namespace App\Controllers\User;

use App\Controllers\Controller;

class LikesController extends Controller{
	public function like($request, $response) {
		$liked_id = $request->getParam('profile');

		$this->likes->saveLike($liked_id);
		exit();
	}
}

?>