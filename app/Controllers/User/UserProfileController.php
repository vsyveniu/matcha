<?php

namespace App\Controllers\User;

use App\Controllers\Controller;
use DateTimeZone;

class UserProfileController extends Controller{
	public function getUserProfile($request, $response){
		$param = $request->getQueryParams();
		if(isset($param['photo'])){
			$this->userProfile->changeMain($param['photo']);
			return $response->withRedirect($this->router->pathFor('user.profile'));
		}
		
		$userProfile = $this->container->userProfile->getUserProfileByUserId($_SESSION['user']);	
		$photos = $this->photos->toArray($userProfile);

		return $this->view->render($response, 'user/profile.twig', [
			'photos' => $photos
		]);
	}

	public function postUserProfile($request, $response){
 		$photos = $this->photos->get($request);

		$profile = array(
			'photos' => $photos,
			'gender' => $request->getParam('gender'),
			'sexualPreferences' => $request->getParam('sexualPreferences'),
			'biography' => $request->getParam('biography'),
			'dateOfBirth' => $request->getParam('dateOfBirth'),
			'mainPhoto' => explode(',', $photos)[0],
			'country' => explode(':', $request->getParam('country'))[1],
			'sity' => explode(':', $request->getParam('city'))[1],
			'state' => explode(':', $request->getParam('state'))[1]
		);

		$this->userProfile->save($profile);
		
		return $response->withRedirect($this->router->pathFor('user.profile'));
	}
}

?>