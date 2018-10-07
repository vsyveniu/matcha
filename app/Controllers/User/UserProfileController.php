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


///old version

/*	public function postUserProfile($request, $response){
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
	}*/


	public function postUserProfile($request, $response)
	{
		$id = $_SESSION['user'];
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

				echo "<pre>";
				print_r($_POST);
				echo "</pre>";


				$str = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
				$data = json_decode(file_get_contents($str), true);
				$latlng = $this->parseLatLng($data);

				$equality = $this->container->userProfile->checkifCoordsEqual($id, $latlng['lat'], $latlng['lng']);
				$autotrigger = $this->container->userProfile->checkAutoTrigger($id);
				$manualtrigger = $this->container->userProfile->checkManTrigger($id);
				var_dump($equality);
				var_dump($autotrigger);
				//if base is void and autotriger is null or always if autotrigger is 1
				if($equality == 0 || $_POST['submethod'] == "auto") 
				{
					if(($lat == "" && $lng == "") || ($lat == "" && $lng != "") || ($lat != "" && $lng == ""))
					{
						//$ip_adress = $_SERVER['REMOTE_ADDR'];
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

				/*	else
					{
						$str = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
						$data = json_decode(file_get_contents($str), true);


					}*/
					$latlng = $this->parseLatLng($data);
					$country = $this->parseCountry($data);
					$state = $this->parseState($data);
					$city = $this->parseCity($data);


					echo "THIS IS AUTO";
					echo "<pre>";
					print_r($latlng);
					 echo "</pre>";
					  echo "<pre>";
					print_r($country);
					 echo "</pre>";
					 echo "<pre>";
					echo $state;
					 echo "</pre>";
					  echo "<pre>";
					echo $city;
					 echo "</pre>";

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




					/*echo "THIS IS MANUAL";
					echo "<pre>";
						print_r($latlng);
						 echo "</pre>";
						  echo "<pre>";
						print_r($country);
						 echo "</pre>";
						 echo "<pre>";
						echo $state;
						 echo "</pre>";
						  echo "<pre>";
						echo $city;
						 echo "</pre>";	
					*/
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
				echo "just show from base";
				return ;
			}
		}
		else
		{

	 		$photos = $this->photos->get($request);

	 		$param = $request->getQueryParams();

	 		/*$paramSet = [];
	 		$paramSet['country'] = explode(':', $_POST['country'])[1];
	 		$paramSet['state'] = explode(':', $_POST['state'])[1];		
	 		$paramSet['city'] = explode(':', $_POST['city'])[1];

	 		$country = $this->standartize($paramSet, "country");
	 		$state = $this->standartize($paramSet, "state");
	 		$city = $this->standartize($paramSet, "city");
	 		$latLng = $this->standartize($paramSet, "latLng");
			*/

			$profile = array(
				'photos' => $photos,
				'gender' => $request->getParam('gender'),
				'sexualPreferences' => $request->getParam('sexualPreferences'),
				'biography' => $request->getParam('biography'),
				'dateOfBirth' => $request->getParam('dateOfBirth'),
				'mainPhoto' => explode(',', $photos)[0],
				'language' => $request->getParam('language')
			);
			$this->userProfile->save($profile);
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
		$result;
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

	public function standartize($paramSet, $method)
	{
		
		if($method == "country")
		{
			$str = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$paramSet['country'].'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
			$dataarr = json_decode(file_get_contents($str), true);
			$result = $this->parseCountry($dataarr);
			return($result['country']);

		}
		else if($method == "state")
		{

			$str = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$paramSet['country'].'+'.$paramSet['state'].'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
			$dataarr = json_decode(file_get_contents($str), true);
			$result = $this->parseState($dataarr);
			return($result);

		}
		else if($method == "city")
		{

			$str = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$paramSet['country'].'+'.$paramSet['state'].'+'.$paramSet['city'].'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
			$dataarr = json_decode(file_get_contents($str), true);
			$result = $this->parseCity($dataarr);
			return($result);

		}
		else if($method == "latLng")
		{

			$str = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$paramSet['country'].'+'.$paramSet['state'].'+'.$paramSet['city'].'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
			$dataarr = json_decode(file_get_contents($str), true);
			$result = $this->parseLatLng($dataarr);
			return($result);

		}
	}



}

?>