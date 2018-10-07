<?php

namespace App\Controllers\User;

use App\Controllers\Controller;

class SearchController extends Controller{
	public function search($request, $response){
		return $this->view->render($response, 'search.twig', [
			'tags' => $this->container->tag->get_all_tags()]);
		
	}

	public function filters($request, $response) {

		$id = $_SESSION['user'];
		if(isset($_POST['method']) && $_POST['method'] == "showGallery")
		{
			return $this->getIntrestingProfiles($_SESSION['user'], $filters = NULL);
		}
		if (isset($_POST['method']) && $_POST['method'] == "setAutoPosition")
		{	
			$dataFromBase = $this->container->search->getPositionFromBase($id);
			
			if(is_null($dataFromBase['lat']) || is_null($dataFromBase['lng']))
			{
				$res = $this->getGeopluginData();
				return $response->withJson($res);
			}
			else
			{

				$str = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$dataFromBase['lat'].','.$dataFromBase['lng'].'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
				$data = json_decode(file_get_contents($str), true);
				$latlng = $this->container->UserProfileController->parseLatLng($data);
				$country = $this->container->UserProfileController->parseCountry($data);
				$state = $this->container->UserProfileController->parseState($data);
				$city = $this->container->UserProfileController->parseCity($data);
				$res['lat'] = $latlng['lat'];
				$res['lng'] = $latlng['lng'];
				$res['country'] = $country['country'];
				$res['state'] = $state;
				$res['city'] = $city;
				$res['manual'] = $dataFromBase['manual'];
				return $response->withJson($res);
			}
		}
		if(isset($_POST['method']) && $_POST['method'] == "getPosition")
		{
			$res = [];
			$lat = $_POST['posLat'];
			$lng = $_POST['posLong'];


			$str = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key=AIzaSyCbq4TwFnX63gTe4O0DytyUU9XOXDEXdIY';
			$data = json_decode(file_get_contents($str), true);
			$latlng = $this->container->UserProfileController->parseLatLng($data);
			$country = $this->container->UserProfileController->parseCountry($data);
			$state = $this->container->UserProfileController->parseState($data);
			$city = $this->container->UserProfileController->parseCity($data);
			$res['country'] = $country['country'];
			$res['country_code'] = $country['country_code'];
			$res['state'] = $state;
			$res['city'] = $city;

			return $response->withJson($res);

		}
		else if (isset($_POST['method']) && $_POST['method'] == "sendData")
		{
			
		$filters = $request->getParsedBody();


		return ($this->getIntrestingProfiles($_SESSION['user'], $filters));
		}
		else
			return $response->withRedirect($this->router->pathFor('search'));
	}

	public function getGeopluginData()
	{
				$res = [];
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
				$latlng = $this->container->UserProfileController->parseLatLng($data);
				$country = $this->container->UserProfileController->parseCountry($data);
				$state = $this->container->UserProfileController->parseState($data);
				$city = $this->container->UserProfileController->parseCity($data);
				$res['lat'] = $latlng['lat'];
				$res['lng'] = $latlng['lng'];
				$res['country'] = $country['country'];
				$res['state'] = $state;
				$res['city'] = $city;
				$res['manual'] = 0;
			return ($res);	
	}

	public function getIntrestingProfiles($id, $filters) {

		$id = $_SESSION['user'];
		$profile = $this->container->userProfile->getUserProfileByUserId($id);
		if($filters == NULL)
			$position = $this->getGeopluginData();
		$target = [];

		$filter = $this->parseFilters($position, $filters);
		$sort = $this->parseSort($filters);


		if (empty($profile))
		{
			$target['gender'] = 'All';
		}
		else
		{
			switch ($profile['gender']) {
				case "Male":
					if($profile['sexualPreferences'] == 'Heterosexual') {
						$target['gender'] = 'Female';
						$target['sexualPreferences'] = 'Heterosexual';
					}
					else if($profile['sexualPreferences'] == 'Homosexual') {
						$target['gender'] = 'Male';
						$target['sexualPreferences'] = 'Homosexual';
					}
					else if($profile['sexualPreferences'] == 'Bisexual') {
						$target['gender'] = 'All';
						$target['sexualPreferences'] = 'Bisexual';
					}
					break;
				
				case "Female":
					if($profile['sexualPreferences'] == 'Heterosexual') {
						$target['gender'] = 'Male';
						$target['sexualPreferences'] = 'Heterosexual';
					}
					else if($profile['sexualPreferences'] == 'Homosexual') {
						$target['gender'] = 'Female';
						$target['sexualPreferences'] = 'Homosexual';
					}
					else if($profile['sexualPreferences'] == 'Bisexual') {
						$target['gender'] = 'All';
						$target['sexualPreferences'] = 'Bisexual';
					}
					break;

			}
		}	


			$filtered = $this->container->search->getFiltered($id, $target, $filter, $sort);

		
		return $filtered;
	}

		public function parseFilters($position, $filters)
	{
		$result = [];
		isset($filters['filter_age']) && $filters['filter_age'] != "none" ? $result['age'] = $filters['filter_age'] : $result['age'] = 0;
		isset($filters['filter_fame']) && $filters['filter_fame'] != "none" ? $result['fame'] = $filters['filter_fame'] : $result['fame'] = 0;
		isset($filters['filter_tags']) ? $result['tags'] = 1 : $result['tags'] = 0;
		isset($filters['age_gap']) && $filters['age_gap'] != "none" ? $result['age_gap'] = $filters['age_gap']: $result['age_gap'] = 0;
		isset($filters['fame_gap']) && $filters['fame_gap'] != "none" ? $result['fame_gap'] = $filters['fame_gap']: $result['fame_gap'] = 0;
		isset($filters['tags_select']) ? $result['tags_select'] = $filters['tags_select'] : $result['tags_select'] = 0;
		isset($filters['location_filter']) && $filters['location_filter'] != "none" ? $result['location_filter'] = $filters['location_filter'] : $result['location_filter'] = 0;
		isset($filters['tags-select']) ? $result['tags_select'] = $filters['tags-select'] : 0;
		if(!$position)
		{
			isset($filters['country']) ? $result['country'] = $filters['country'] : $result['country'] = 0;
			isset($filters['state']) ? $result['state'] = $filters['state'] : $result['state'] = 0;
			isset($filters['city']) ? $result['city'] = $filters['city'] : $result['city'] = 0;
		}
		else if($position)
		{
			$result['country'] = $position['country'];
			$result['state'] = $position['state'];
			$result['city'] = $position['city'];
		}	
		return $result;
	}

	public function parseSort($filters)
	{
		$result = [];
		isset($filters['sort_age']) ? $result['age'] = 1 : 0;
		isset($filters['sort_fame']) ? $result['fame'] = 1 : 0;
		isset($filters['sort_tags']) ? $result['tags'] = 1 : 0;
		return $result;
	}


}

?>