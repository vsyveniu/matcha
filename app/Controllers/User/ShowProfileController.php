<?php

namespace App\Controllers\User;

use App\Controllers\Controller;

class ShowProfileController extends Controller{
	public function show($request, $response){
		$param = $request->getQueryParams();
		$profile = $this->container->userProfile->getUserProfileById($param['profile']);

		$photos = $this->photos->toArray($profile[0]);
		if(isset($param['profile'])) {
			if($profile){
				$this->container->visits->save($param['profile']);
				$this->container->fame_rating->update($param['profile']);
				//$this->sendEmail->visitedNotification($profile[0]);
				$this->view->getEnvironment()->addGlobal('like', $this->likes->findLike($param['profile'], $_SESSION['user']));

				return $this->view->render($response, 'show.twig', [
					'photos' => $photos,
					'profile' => $profile[0],
					'fame_rating' => $this->fame_rating->getRating($param['profile'])
				]);
			}
		}

		return $response->withRedirect($this->router->pathFor('search'));
	}
}

?>