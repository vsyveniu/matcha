<?php

namespace App\Models;

class UserProfile{
	private $db;

	public function __construct($db){
		$this->db = $db;
	}

	public function getUserProfileByUserId($id){
		$sql = "SELECT * FROM `user_profile`
				WHERE `user_id` = :user_id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':user_id' => $id));

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function getUserProfileById($id){
		$sql = "SELECT users.email, users.firstName, users.lastName, user_profile.photos, user_profile.gender, user_profile.biography, 
					user_profile.dateOfBirth, user_profile.mainPhoto, user_profile.id,
					user_profile.gender, user_profile.country, user_profile.sity, user_profile.state,
					TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) AS age,
					IF(TIMESTAMPDIFF(MINUTE, users.lastActivity, NOW()) > 15, users.lastActivity, 'ONLINE') as status
					FROM `user_profile`
					INNER JOIN `users` ON user_profile.user_id= users.id
					WHERE user_profile.id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':id' => $id));

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getAllUsersProfiles($target = NULL, $filters = NULL){
		$sql = "SELECT users.id,users.email, users.firstName, users.lastName, user_profile.photos,
					user_profile.gender, user_profile.biography, 
					user_profile.dateOfBirth, user_profile.mainPhoto, user_profile.id, fame_rating.rating, user_profile.country, user_profile.state, user_profile.sity,
					TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) AS age,
					IF(TIMESTAMPDIFF(MINUTE, users.lastActivity, NOW()) > 15, 'OFLINE', 'ONLINE') AS status
					FROM `user_profile`
					INNER JOIN `users` 
						ON user_profile.user_id = users.id
					INNER JOIN `fame_rating`
						ON users.id = fame_rating.user_id
					WHERE users.id <> :user_id
					AND user_profile.mainPhoto IS NOT NULL";
		if($target) {
			$sql .= " AND user_profile.gender = :gender
					 AND (user_profile.sexualPreferences = :sexualPreferences_0
					 OR user_profile.sexualPreferences = :sexualPreferences_1)";
		}	
		if($filters) {
			foreach ($filters as $key => $filter) {
				if ($key == 'min_age')
					$sql .= " AND TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) >= :min_age";
				if ($key == 'max_age')
					$sql .= " AND TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) <= :max_age";
				if ($key == 'min_fame')
					$sql .= " AND fame_rating.rating >= :min_fame";
				if ($key == 'country' && $filter)
					$sql .= " AND user_profile.country = :country";
				if ($key == 'state' && $filter)
					$sql .= " AND user_profile.state = :state";
				if ($key == 'city' && $filter)
					$sql .= " AND user_profile.city = :city";
				if ($key == 'tags-select' && $filter && is_array($filter)) {
					$sql .= " AND EXIST 
							(
								SELECT tags.tag, users_tags.user_id 
								FROM `tags`
								INNER JOIN `users_tags`
									ON tags.id = users_tags.tag_id 
								WHERE users.id = users_tags.user_id
							";
					foreach ($filter as $k => $tag) {
						$sql .= " AND tags.tag = :tag".$k;
					}
					$sql .= ")";
				}
			}
		}
		$stmt = $this->db->prepare($sql);
		if($target){
			$stmt->bindParam(':gender', $target['gender']);
			$stmt->bindParam(':sexualPreferences_0', $target['sexualPreferences'][0]);
			$stmt->bindParam(':sexualPreferences_1', $target['sexualPreferences'][1]);
		}
		if($filters) {
			$stmt->bindParam(':min_age', $filters['min_age']);
			$stmt->bindParam(':max_age', $filters['max_age']);
			$stmt->bindParam(':min_fame', $filters['min_fame']);
			if($filters['country'])
				$stmt->bindParam(':country', $filters['country']);
			if($filters['state'])
				$stmt->bindParam(':state', $filters['state']);
			if($filters['city'])
				$stmt->bindParam(':city', $filters['city']);
			if($filters['tags-select']) {
				foreach ($filters['tags-select'] as $key => $tag) {
					$stmt->bindParam(':tag'.$key, $tag);
				}
			}
		}
		$stmt->bindParam(':user_id', $_SESSION['user']);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	public function getIntrestingProfiles($id, $filters = NULL) {
		$profile = $this->getUserProfileByUserId($id);
		switch ($profile['gender']) {
			case 'Male':
				if($profile['sexualPreferences'] == 'Heterosexual') {
					$target['gender'] = 'Female';
					$target['sexualPreferences'][0] = 'Heterosexual';
					$target['sexualPreferences'][1] = 'Bisexual';
				}
				else if($profile['sexualPreferences'] == 'Homosexual') {
					$target['gender'] = 'Male';
					$target['sexualPreferences'][0] = 'Homosexual';
					$target['sexualPreferences'][1] = 'Bisexual';
				}
				break;
			
			case 'Female':
				if($profile['sexualPreferences'] == 'Heterosexual') {
					$target['gender'] = 'Male';
					$target['sexualPreferences'][0] = 'Heterosexual';
					$target['sexualPreferences'][1] = 'Bisexual';
				}
				else if($profile['sexualPreferences'] == 'Homosexual') {
					$target['gender'] = 'Female';
					$target['sexualPreferences'][0] = 'Homosexual';
					$target['sexualPreferences'][1] = 'Bisexual';
				}
				break;
		}

		return $this->getAllUsersProfiles($target, $filters);
	}

	public function save($profile) {
		foreach($profile as $key => $value) {
			if($value) {
				if (!$this->getUserProfileByUserId($_SESSION['user'])) {
					$sql = "INSERT INTO `user_profile`
							(`".$key."`, `user_id`)
							VALUES (:value, :user_id)";
				}
				else
					$sql = "UPDATE `user_profile` SET
						`".$key."` = :value
						WHERE `user_id` = :user_id";
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(':value', $value);
				$stmt->bindParam(':user_id', $_SESSION['user']);
				$stmt->execute();
			}
		}
	}

	public function changeMain($newMain){
		$profile = $this->getUserProfileByUserId($_SESSION['user']);
		$photos = explode(',', $profile['photos']);
		$newMain = (int)$newMain;
		if(is_int($newMain) && $newMain <= count($photos) && $newMain > 0) {
			$tmp = $photos[0];
			$photos[0] = $photos[$newMain];
			$photos[$newMain] = $tmp;
			$new['photos'] = implode(',', $photos);
			$new['mainPhoto'] = $photos[0];
			$this->save($new);
		}
	}
}

?>