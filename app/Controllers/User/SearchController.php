<?php

namespace App\Controllers\User;

use App\Controllers\Controller;

class SearchController extends Controller{
	public function search($request, $response){
		return $this->view->render($response, 'search.twig', [
			'intresting_profiles' => $this->userProfile->getIntrestingProfiles($_SESSION['user']),
			'tags' => $this->tag->get_all_tags()
		]);
	}

	public function filters($request, $response) {
		$filters = $request->getParsedBody();
		$filtered = $this->userProfile->getIntrestingProfiles($_SESSION['user'], $filters);
		return $this->view->render($response, 'search.twig', [
			'intresting_profiles' => $filtered,
			'tags' => $this->tag->get_all_tags()
		]);
	}
}

?>