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


		

		$country = $filter['country'];
		$state = $filter['state'];
		$city = $filter['city'];
		$age = $filter['age'];
		$fame = $filter['fame'];
		$location_filter = $filter['location_filter'];
		$tags_filter = $filter['tags'];

		$filter_age_gap = explode("-", $filter['age_gap']);
		$min_age = $filter_age_gap[0];
		$max_age = $filter_age_gap[1];
		$filter_fame_gap = explode("-", $filter['fame_gap']);
		$min_fame = $filter_fame_gap[0];
		$max_fame = $filter_fame_gap[1];

		$sort_age = $sort['sort_age'];
		$sort_fame = $sort['sort_fame'];
		$sort_tags = $sort['sort_tags'];
		if(!empty($filter['tags_select']))
		{
			foreach ($filter['tags_select'] as &$value)
			 {
			 	$value = addcslashes($value, "'");
				$value = "'".$value."'";
			}
			$tags_str = implode(",", $filter['tags_select']);

			$tags_str = str_replace(",", " OR tag = ", $tags_str);	

			$sql = "SELECT id FROM tags WHERE tag =  $tags_str";

			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$tags_id = $stmt->fetchAll();
			$tags_id = array_map(function($elem){return $elem['id'];}, $tags_id);
			$tags_str = implode(",", $tags_id);
		}
		
		

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

		($gender != "All") ? $sql .=" 
					 WHERE user_profile.gender = '$gender' 
					AND users.id <> '$id'
					AND user_profile.sexualPreferences = '$prefer'" : 0;
							 


		($gender == "All" && $prefer != NULL) ? $sql .=" 
					WHERE user_profile.sexualPreferences = '$prefer'
					AND users.id <> '$id' " : 0;

		$sql .="AND NOT EXISTS (SELECT * FROM blocked WHERE blocked_id = users.id AND blocker_id = '$id') AND user_profile.filled = 1 ";			

		($age != 0 ) ? $sql .="
				AND TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) = '$age'" : 0;

		(!empty($filter['tags_select'])) ? $sql .=" AND users.id IN (SELECT user_id FROM `users_tags` WHERE tag_id IN ($tags_str)) " : 0; 

		($filter['age_gap'] != 0 && $age == 0) ? $sql .="
				AND TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) BETWEEN '$min_age' AND '$max_age'" : 0;		

		($location_filter === "City" ) ? $sql .=" 
								AND user_position.city = '$city' AND user_position.city != ''" : 0;
		($location_filter === "State" ) ? $sql .=" 
								AND user_position.state = '$state' AND user_position.state != ''" : 0;
		($location_filter === "Country" ) ? $sql .=" 
								AND user_position.country = '$country' AND user_position.country != ''" : 0;
		($fame != 0 ) ? $sql .="
								AND fame_rating.rating = '$fame'" : 0;

		($filter['fame_gap'] != 0 && $fame == 0) ? $sql .="
				AND fame_rating.rating BETWEEN '$min_fame' AND '$max_fame'" : 0;						

		($tags_filter == 1) ? $sql .=" AND (SELECT COUNT(DISTINCT t1.tag_id)
						                FROM `users_tags` AS t1 INNER JOIN `users_tags` AS t2 ON t1.tag_id = t2.tag_id
						                WHERE t2.user_id = '$id' AND t1.user_id = users.id) > 0" : 0;					

 		$sql .= " 
 					ORDER BY CASE WHEN user_position.city = '$city' AND user_position.city != '' THEN 1 ELSE 2 END, 
					CASE WHEN user_position.state = '$state' AND user_position.state != '' THEN 1 ELSE 2 END,
					CASE WHEN user_position.country = '$country'  AND user_position.country != '' THEN 1 ELSE 2 END,";


 		($sort_age === "ascending")	? $sql .= " age ASC, " : 0;		
		($sort_age === "descending") ? $sql .= " age DESC, " : 0;

		($sort_fame === "ascending") ? $sql .= " fame_rating.rating ASC, " : 0;
		($sort_fame === "descending") ? $sql .= " fame_rating.rating DESC, " : 0;	

		($sort_tags == "less") ? $sql .=" count ASC" : 0;	
		($sort_tags == "more") ? $sql .=" count DESC" : 0;	
		($sort_fame === 0) ? $sql .= ", fame_rating.rating DESC" : 0;				
		
	

	
		
		
		
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

	public function get_fame_max()
	{
		$sql = "SELECT MAX(rating) AS maxfame FROM fame_rating";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch();
		return($res['maxfame']);
	}

	public function get_age_max()
	{
		$sql = "SELECT MAX(TIMESTAMPDIFF(YEAR, dateOfBirth, CURDATE())) AS maxage FROM user_profile";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch();
		return($res['maxage']);
	}

	public function block_user($id, $blocker)
	{
		$id = (int)$id;
		$blocker = (int)$blocker;
		if(!$this->blocked_already($id, $blocker))
		{
			
			$sql = "INSERT INTO blocked (`blocked_id`, `blocker_id`) VALUES ('$id', '$blocker')";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			return (1);
		}
		return 0;		
	}

	private function blocked_already($id, $blocker)
	{
			$sql = "SELECT * FROM `blocked` WHERE `blocked_id` = '$id' AND `blocker_id` = '$blocker'";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$res = $stmt->fetch();
			return ($res);
	}

}	
?>