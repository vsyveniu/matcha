<?php

namespace App\Controllers;

class GeoController extends Controller {
	public function getCountry($request, $response){
		$countryId = $request->getParam('countryId')  ? explode(':', $request->getParam('countryId'))[0] : 0;
		$stateId = $request->getParam('stateId')  ? explode(':', $request->getParam('stateId'))[0] : 0;
		$command = $request->getParam('get') ? $request->getParam('get') : "";

		switch($command){
			case "country":
				$statement = "SELECT `id`, `name` FROM `countries`";
				break;
			case "state":
				$statement = "SELECT `id`, `name` FROM `states` WHERE `country_id`=".$countryId;
				break;
			case "city":
				$statement = "SELECT `id`, `name` FROM `cities` WHERE `state_id`=".$stateId;
				break;
			default:
				break;
		}
		
		$sth = $this->db->prepare($statement);
		$sth->execute();
		
		$result = $sth->fetchAll();
		echo json_encode($result);
		exit();
	}

}