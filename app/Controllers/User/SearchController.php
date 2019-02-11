<?php

namespace App\Controllers\User;

use App\Controllers\Controller;

class SearchController extends Controller{
	public function search($request, $response)
	{
		if(!$this->auth->is_filled()){
			return $this->view->render($response, 'search_basic.twig');
		}

		$age_max = $this->container->search->get_age_max();
		$fame_max = $this->container->search->get_fame_max();
		$age_max = (int)$age_max;
		$fame_max = (int)$fame_max;

		return $this->view->render($response, 'search.twig', [
			'tags' => $this->container->tag->get_all_tags(),
			'age' => $age_max,
			'fame' => $fame_max
		]);
		
	}




	public function filters($request, $response) {
		$id = $_SESSION['user'];

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
			$res['country'] = htmlspecialchars($country['country']);
			$res['country_code'] = $country['country_code'];
			$res['state'] = $state;
			$res['city'] = $city;

			return $response->withJson($res);

		}
		if(isset($_POST['method']) && $_POST['method'] == "fuck")
		{
			$filters = $request->getParsedBody();
			return ;
		}
		if(isset($_POST['method']) && $_POST['method'] == "showGalleryBasic")
		{
			return $this->basic_getIntrestingProfiles();
		}
		if(isset($_POST['method']) && $_POST['method'] == "showGallery")
		{
			$errors = 0;
			$filters = $request->getParsedBody();
		

			if($filters['location_filter'] == "none" || $filters['location_filter'] == "City" || $filters['location_filter'] == "State" || $filters['location_filter'] == "Country")
				;
			else
				$errors = 1;
			if($filters['sort_fame'] == "none" || $filters['sort_fame'] == "ascending" || $filters['sort_fame'] == "descending")
				;
			else
				$errors = 1;
			if($filters['sort_tags'] == "more" || $filters['sort_tags'] == "less")
				;
			else
				$errors = 1;
			if($filters['sort_age'] == "none" || $filters['sort_age'] == "ascending" || $filters['sort_age'] == "descending")
				;
			else
				$errors = 1;
			if($filters['filter_age'] == "none" || is_numeric($filters['filter_age']))
				;
			else
				$errors = 1;
			if($filters['filter_fame'] == "none" || is_numeric($filters['filter_fame']))
				;
			else
				$errors = 1;
			if($filters['age_gap'] == "none" || preg_match('(^\d{1,3}\s{1}[-]{1}\s{1}\d{1,3}$)', $filters['age_gap']))
				;
			else
				$errors = 1;
			if($filters['fame_gap'] == "none" || preg_match('(^\d{1,9}\s{1}[-]{1}\s{1}\d{1,9}$)', $filters['fame_gap']))
				;
			else
				$errors = 1;
			if($errors == 1)
			{
				
				return $response->withJson("error");
			}
			else
			{
				$filters['country'] = htmlspecialchars($filters['country']);
				$filters['city'] = htmlspecialchars($filters['city']);
				$filters['state'] = htmlspecialchars($filters['state']);
				return $this->getIntrestingProfiles($_SESSION['user'], $filters);
			}	
		}
		if(isset($_POST['method']) && $_POST['method'] == "getMaxValues")
		{
			$res = [];
			$age_max = $this->container->search->get_age_max();
			$fame_max = $this->container->search->get_fame_max();
			$age_max = (int)$age_max;
			$fame_max = (int)$fame_max;
			array_push($res, $age_max);
			array_push($res, $fame_max);
			return $response->withJson($res);
		}
		else
			return $response->withRedirect($this->router->pathFor('search'));
	}

	public function getGeopluginData()
	{
				$res = [];
				$ip_adress = $_SERVER['HTTP_CLIENT_IP']; 
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

	public function basic_getIntrestingProfiles()
	 {
	
		$id = $_SESSION['user'];

		$filtered = $this->container->BasicSearch->getFiltered($id);
		return $filtered;

	}

	public function getIntrestingProfiles($id, $filters) {
	
		$position = NULL;
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
			$target['sexualPreferences'] = 'Bisexual';
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
		isset($filters['sort_age']) && $filters['sort_age'] != "none" ? $result['sort_age'] = $filters['sort_age'] : $result['sort_age'] = 0;
		isset($filters['sort_fame']) && $filters['sort_fame'] != "none" ? $result['sort_fame'] = $filters['sort_fame'] : $result['sort_fame'] = 0;
		isset($filters['sort_tags']) ? $result['sort_tags'] = $filters['sort_tags'] : 0;
		return $result;
	}


}

?>