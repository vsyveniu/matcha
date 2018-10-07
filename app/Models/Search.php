<?php


namespace App\Models;

class Search{
	private $db;

	public function __construct($db){
		$this->db = $db;
	}


	public function getFiltered($id, $target, $filter, $sort)
	{
		$gender = $target['gender'];
		$prefer = $target['sexualPreferences'];


		$sql = "SELECT tag_id FROM `users_tags` WHERE user_id =  '$id'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetchAll();

		$country = $filter['country'];
		$state = $filter['state'];
		$city = $filter['city'];

	

		$sql = "SELECT users.id,users.email, users.firstName, users.lastName, user_profile.photos,
					user_profile.gender, user_profile.biography, 
					user_profile.dateOfBirth, user_profile.mainPhoto, user_profile.id, user_profile.sexualPreferences,
					user_position.country, user_position.state, user_position.city, user_position.lat, user_position.lng,
					fame_rating.rating, (SELECT COUNT(DISTINCT t1.tag_id)
						                FROM `users_tags` AS t1 INNER JOIN `users_tags` AS t2 ON t1.tag_id = t2.tag_id
						                WHERE t2.user_id = '$id' AND t1.user_id = users.id) AS count,
					TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) AS age,
					IF(TIMESTAMPDIFF(MINUTE, users.lastActivity, NOW()) > 15, 'OFFLINE', 'ONLINE') AS status
					FROM `user_profile`
 					INNER JOIN `users` ON user_profile.user_id = users.id
 					INNER JOIN `user_position` ON user_position.user_id = users.id	
 					INNER JOIN `fame_rating` ON fame_rating.user_id = users.id ";

 		/*(!$prefer) ? $sql .="WHERE user_profile.gender = '$gender' 
							 AND users.id <> '$id'
							 AND user_profile.sexualPreferences = '$prefer'" : 0;	*/

		($gender != "All") ? $sql .=" 
					 WHERE user_profile.gender = '$gender' 
					AND users.id <> '$id'
					AND user_profile.sexualPreferences = '$prefer'" : 0; 					 


		($gender == "All" && $prefer != NULL) ? $sql .="WHERE user_profile.sexualPreferences = '$prefer'" : 0;

 		$sql .= " 
 					ORDER BY 
					CASE WHEN user_position.city = '$city' THEN 1 ELSE 2 END, 
					CASE WHEN user_position.state = '$state' THEN 1 ELSE 2 END, 
					CASE WHEN user_position.country = '$country' THEN 1 ELSE 2 END,
					count DESC, 
					fame_rating.rating DESC";			


	
											























	/*		if(!$prefer)
		{
			$sql = "SELECT users.id,users.email, users.firstName, users.lastName, user_profile.photos,
					user_profile.gender, user_profile.biography, 
					user_profile.dateOfBirth, user_profile.mainPhoto, user_profile.id, user_profile.sexualPreferences,
					user_position.country, user_position.state, user_position.city, user_position.lat, user_position.lng,
					fame_rating.rating, 
					TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) AS age,
					IF(TIMESTAMPDIFF(MINUTE, users.lastActivity, NOW()) > 15, 'OFFLINE', 'ONLINE') AS status
					FROM `user_profile`
 					INNER JOIN `users` ON user_profile.user_id = users.id
 					INNER JOIN `user_position` ON user_position.user_id = users.id	
 					INNER JOIN `fame_rating` ON fame_rating.user_id = users.id ORDER BY fame_rating.rating DESC";

		}
		else if($gender != "All")
		{
			$sql = "SELECT users.id,users.email, users.firstName, users.lastName, user_profile.photos,
					user_profile.gender, user_profile.biography, 
					user_profile.dateOfBirth, user_profile.mainPhoto, user_profile.id, user_profile.sexualPreferences,
					user_position.country, user_position.state, user_position.city, user_position.lat, user_position.lng,
					fame_rating.rating, (SELECT COUNT(DISTINCT t1.tag_id)
						                FROM `users_tags` AS t1 INNER JOIN `users_tags` AS t2 ON t1.tag_id = t2.tag_id
						                WHERE t2.user_id = '$id' AND t1.user_id = users.id) AS count,
					TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) AS age,
					IF(TIMESTAMPDIFF(MINUTE, users.lastActivity, NOW()) > 15, 'OFFLINE', 'ONLINE') AS status
					FROM `user_profile` 
					INNER JOIN `users` ON user_profile.user_id = users.id 
					INNER JOIN `user_position` ON user_position.user_id = users.id
					INNER JOIN `fame_rating` ON fame_rating.user_id = users.id
					WHERE user_profile.gender = '$gender' 
							 AND users.id <> '$id'
							 AND user_profile.sexualPreferences = '$prefer'
					ORDER BY 
							CASE WHEN user_position.city = 'Kulykiv' THEN 1 ELSE 2 END,
							CASE WHEN user_position.state = 'Donetsk Oblast' THEN 1 ELSE 2 END,
							CASE WHEN user_position.country = 'Ukraine' THEN 1 ELSE 2 END,
							count DESC, 
							fame_rating.rating DESC
					;";

		}			
		else if($gender == "All" && $prefer != NULL)
		{
			$sql = "SELECT users.id,users.email, users.firstName, users.lastName, user_profile.photos,
					user_profile.gender, user_profile.biography, 
					user_profile.dateOfBirth, user_profile.mainPhoto, user_profile.id, user_profile.sexualPreferences,
					user_position.country, user_position.state, user_position.city, user_position.lat, user_position.lng, 
					fame_rating.rating, 
					TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) AS age,
					IF(TIMESTAMPDIFF(MINUTE, users.lastActivity, NOW()) > 15, 'OFFLINE', 'ONLINE') AS status
					FROM `user_profile`
 					INNER JOIN `users` ON user_profile.user_id = users.id
 					INNER JOIN `user_position` ON user_position.user_id = users.id
 					INNER JOIN `fame_rating` ON fame_rating.user_id = users.id
 					WHERE user_profile.sexualPreferences = '$prefer' ORDER BY fame_rating.rating DESC";

		}
			*/
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		
		$res = json_encode($res);

		return $res;
	}

	



	public function getPositionFromBase($id)
	{
		$data = [];
		$sql = "SELECT `lat` FROM `user_position` WHERE `user_id` = '$id'";
		$sql1 = "SELECT `lng` FROM `user_position` WHERE `user_id` = '$id'";
		$sql2 = "SELECT `manualset` FROM `user_position` WHERE `user_id` = '$id'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		$data['lat'] = $res['lat'];
		$stmt = $this->db->prepare($sql1);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		$data['lng'] = $res['lng'];
		$stmt = $this->db->prepare($sql2);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		$data['manual'] = $res['manualset'];
		return($data);
	}

}	
?>