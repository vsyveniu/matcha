<?php


namespace App\Models;

class BasicSearch{
	private $db;

	public function __construct($db){
		$this->db = $db;
	}


	public function getFiltered($id)
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
					WHERE users.id <> '$id'
					AND user_profile.filled = 1";

		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		


		$res = json_encode($res);

		return $res;
	}

}	
?>