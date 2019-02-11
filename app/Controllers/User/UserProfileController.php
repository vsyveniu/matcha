<?php

namespace App\Controllers\User;

use App\Controllers\Controller;
use DateTimeZone;

class UserProfileController extends Controller{
	public function getUserProfile($request, $response){
		$param = $request->getQueryParams();
		if(isset($param['photo']))
		{
			$this->userProfile->changeMain($param['photo']);
			return $response->withRedirect($this->router->pathFor('user.profile'));
		}
		
		$userProfile = $this->container->userProfile->getUserProfileByUserId($_SESSION['user']);
		$biography = $this->container->userProfile->get_biography($_SESSION['user']);
		$fame = $this->container->fame_rating->getRating($_SESSION['user']);
		$photos = $this->photos->toArray($userProfile);
		$count = count($photos);
		$tags = $this->container->tag->get_tags($_SESSION['user']);
		$tags = array_map(function($elem){return $elem['tag'];}, $tags);
		$tags = array_unique($tags);
		foreach ($tags as $key => &$value) {
			$value = '#'.$value;
		}
		return $this->view->render($response, 'user/profile.twig', [
			'profile' => $userProfile,
			'photos' => $photos,
			'tags' => $tags,
			'len' => $count,
			'fame' => $fame,
			'biography' => $biography
		]);
	}


	public function blacklist($request, $response)
	{
			$id = $_SESSION['user'];
			$res = [];
			$list = $this->container->userProfile->get_blacklist($id);
			
			return $this->view->render($response, 'user/blacklist.twig', [
				'list' => $list
			]);
	}
	public function blacklistPost($request, $response)
	{		
			$param = $request->getParsedBody();
			if(isset($param['blacklist_submit']))
			{
				$blocker_id = $_SESSION['user'];
				$id = (int)$param['blacklist_name'];
				$this->container->userProfile->unblock($id, $blocker_id);
				$this->chats->enable($blocker_id, $id);
			}

			return $response->withRedirect($this->router->pathFor('user.blacklist'));

	}		

	public function error($request, $response)
	{		
	
			return $this->view->render($response, 'user/error.twig');

	}	

	public function postUserProfile($request, $response)
	{
		$id = $_SESSION['user'];
		$error = 0;

		foreach ($_POST as &$value) {
			$value = htmlspecialchars($value);
		}
		if (isset($_POST['gender']) && $_POST['gender'] == "Male" || $_POST['gender'] == "Female" ) 
			;
		else if(isset($_POST['gender']))
			$error = 1;
		if (isset($_POST['sexualPreferences']) && $_POST['sexualPreferences'] == "Homosexual" || $_POST['sexualPreferences'] == "Bisexual" || $_POST['sexualPreferences'] == "Heterosexual") 
			;
		else if(isset($_POST['sexualPreferences']))
			$error = 1;
		if($error == 1)
		{
			return $response->withRedirect($this->router->pathFor('user.error'));
		}

		if(isset($_POST['method']) && $_POST['method'] == "addTag")
		{

			$tags = $_POST['tags'];
			$split = explode(",", $tags);
			$newarr = [];
			foreach ($split as $key => $value)
			{
			 	if($value != "")
					array_push($newarr, trim(ltrim($value, "#"), " "));
			}
			foreach ($newarr as $key => $value)
			{
				$this->container->tag->save($value, $id);
			}
			return ;
		}
		if(isset($_POST['method']) && $_POST['method'] == "showTag")
		{
			$tags = $this->container->tag->get_tags($id);
			$res = [];
			$new = [];
			foreach ($tags as $key => &$value)
			{
			 	$new['tag'] = '#'.$value['tag'];
			 	$new['id'] = $value['id'];
			 	array_push($res, $new);
			}
			return $response->withJson($res);
		}
		if(isset($_POST['method']) && $_POST['method'] == "delTag")
		{
			$tag_id = (int)$_POST['name'];

			$this->container->tag->del_tags($tag_id, $id);

			$tags = $this->container->tag->get_tags($id);
					
			$res = [];
			$new = [];
			foreach ($tags as $key => $value)
			{	
						
			 	$new['tag'] = '#'.$value['tag'];
			 	$new['id'] = $value['id'];
			 	array_push($res, $new);
			}
			return $response->withJson($res);

		}
		if(isset($_POST['submethod']) && $_POST['submethod'] == "manual")
		{
			$this->userProfile->switchTrigger($id, "manual");
			$_SESSION['trigger'] = 1;
		}
		if(isset($_POST['submethod']) && $_POST['submethod'] == "auto")
			$this->userProfile->switchTrigger($id, "auto");

		if(isset($_POST['method']) && $_POST['method'] == "getPosition")
		{
			$data = $this->search->getPositionFromBase($id);
			return $response->withJson($data);
		}
		if(isset($_POST['method']) && $_POST['method'] == "position")
		{
				$lat = $_POST['posLat'];
				$lng = $_POST['posLong'];

				$str = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
				$data = json_decode(file_get_contents($str), true);
				$latlng = $this->parseLatLng($data);

				$equality = $this->container->userProfile->checkifCoordsEqual($id, $latlng['lat'], $latlng['lng']);
				$autotrigger = $this->container->userProfile->checkAutoTrigger($id);
				$manualtrigger = $this->container->userProfile->checkManTrigger($id);
				if($equality == 0 || $_POST['submethod'] == "auto") 
				{
					if(($lat == "" && $lng == "") || ($lat == "" && $lng != "") || ($lat != "" && $lng == ""))
					{
						$ip_adress = $_SERVER['HTTP_CLIENT_IP'];  ///not sure about it, but remote_addr(seems like most reasonable) isn't working
						$str = 'http://www.geoplugin.net/php.gp?ip='.$ip_adress;
						$data = unserialize(file_get_contents($str));
						if(!empty($data['geoplugin_latitude']) && !empty($data['geoplugin_longitude']))
						{
							$lat = $data['geoplugin_latitude'];
							$lng = $data['geoplugin_longitude'];
						}	
						$str = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
						$data = json_decode(file_get_contents($str), true);
					}

					$latlng = $this->parseLatLng($data);
					$country = $this->parseCountry($data);
					$state = $this->parseState($data);
					$city = $this->parseCity($data);


					 if(!$autotrigger)
						$this->userProfile->savePosition($id, $latlng, $country, $state, $city, "save");
					else
						$this->userProfile->savePosition($id, $latlng, $country, $state, $city, "update");

					return $response->withJson($result);
				}

				else if($equality == 2 && $_POST['submethod'] == "manual")
				{
					$str = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
					$data = json_decode(file_get_contents($str), true);
					$latlng = $this->parseLatLng($data);
					$country = $this->parseCountry($data);
					$state = $this->parseState($data);
					$city = $this->parseCity($data);




					 	if(!$latlng)
						 {
						 	return $response->withJson("Seems like you are a fish. Sorry, but we are not provide our services to a sea sitizens");
						 }
						 if(!$city)
						 {
						 	$result = 'Your location is: '.$country['country'].', '.$state.'<br>If you do not specify city, you will not be capable to filter profiles by city';
						 }
						 else
						 	$result = 'Your location is: '.$country['country'].', '.$state.', '.$city;
					
					$this->userProfile->savePosition($id, $latlng, $country, $state, $city, "update");

				return $response->withJson($result);
			}
			else if($equality == 1 || $manualtrigger == 1)
			{
				return ;
			}
		}
		else
		{
			if(!$request->getParam('dateOfBirth') || ( $request->getParam('dateOfBirth')&& !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$request->getParam('dateOfBirth'))))
				return $response->withRedirect($this->router->pathFor('user.profile'));
	 		$photos = $this->photos->get($request);

	 		$param = $request->getParsedBody();
	 		$userData = array(
	 			'firstName' => $param['firstName'],
	 			'lastName' => $param['lastName'],
	 			'mail' => $param['email']
	 		);
	 		$this->userProfile->saveUserData($userData, $id);
			$profile = array(
				'photos' => $photos,
				'gender' => htmlspecialchars($request->getParam('gender')),
				'sexualPreferences' => htmlspecialchars($request->getParam('sexualPreferences')),
				'biography' => htmlspecialchars($request->getParam('biography')),
				'dateOfBirth' => htmlspecialchars($request->getParam('dateOfBirth')),
				'mainPhoto' => explode(',', $photos)[0]
			);
			$this->userProfile->save($profile);
			if($photos){
				$this->userProfile->mark_profile_as_filled($_SESSION['user']);
			}
			return $response->withRedirect($this->router->pathFor('user.profile'));
		}
		
	return $response->withRedirect($this->router->pathFor('user.profile'));
}


	public function parseLatLng($data)
	{
		$result = [];
		foreach ($data['results'] as $level1){
			 	if(!empty($level1['geometry'])){
			 		if(!empty($level1['geometry']['location'])){
			 			if(!empty($level1['geometry']['location']['lat']) && !empty($level1['geometry']['location']['lng'])){
			 				$result['lat'] = $level1['geometry']['location']['lat'];
			 				$result['lng'] = $level1['geometry']['location']['lng'];
			 				break;
			 			}	
			 		}
			 		
			 	}

	}
	return($result);
	}


	public function parseCountry($data)
	{
		$result = [];
		foreach ($data['results'] as $level1){
			 	if(!empty($level1['address_components'])){
			 		foreach ($level1['address_components'] as $level2) {
			 			foreach ($level2 as $level3){
			 				
			 				if(!empty($level2['types'])){
			 					if(!empty($level2['types']) && $level2['types'][0] == "country"){
			 						if(!empty($level2['long_name']))
			 						{
			 							$result['country'] = $level2['long_name'];
			 							$result['country_code'] = $level2['short_name'];
			 						}
			 						break;
			 					}
			 				}
			 			}
			 		}

			 	}	

			}
		return($result);
	}


	public function parseState($data)
	{
		$result;
		foreach ($data['results'] as $level1){
			 	if(!empty($level1['address_components'])){
			 		foreach ($level1['address_components'] as $level2) {
			 			foreach ($level2 as $level3){
			 				if(!empty($level2['types'])){
			 					if(!empty($level2['types']) && $level2['types'][0] == "administrative_area_level_1"){
			 						if(!empty($level2['long_name']))
			 							$result = $level2['long_name'];
			 						break;
			 					}
			 				}
			 			}
			 		}

			 	}	

			}
		return($result);
	}


	public function parseCity($data)
	{
		$result = NUll;
		foreach ($data['results'] as $level1){
			 	if(!empty($level1['address_components'])){
			 		foreach ($level1['address_components'] as $level2) {
			 			foreach ($level2 as $level3){
			 				if(!empty($level2['types'])){
			 					if(!empty($level2['types']) && $level2['types'][0] == "locality"){
			 						if(!empty($level2['long_name']))
			 							$result = $level2['long_name'];
			 						break;
			 					}
			 				}
			 			}
			 		}

			 	}	

			}
		return($result);
	}

	public function fake($request, $response){
		$param =$request->getParsedBody();
		if($param['id'] && !$this->fake->exist($_SESSION['user'], $param['id'])){
			$this->fake->mark($_SESSION['user'], $param['id']);
		}
	}
}

?>