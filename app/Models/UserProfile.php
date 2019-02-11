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
					user_profile.gender, user_profile.country, user_profile.sity, user_profile.state, user_profile.sexualPreferences,
					TIMESTAMPDIFF(YEAR, user_profile.dateOfBirth, CURDATE()) AS age,
					IF(TIMESTAMPDIFF(MINUTE, users.lastActivity, NOW()) > 15, users.lastActivity, 'ONLINE') as status
					FROM `user_profile`
					INNER JOIN `users` ON user_profile.user_id= users.id
					WHERE user_profile.id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':id' => $id));

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getUserDataByUserId($id){
		$sql = "SELECT * FROM `users`
				WHERE `id` = '$id'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
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

	public function saveUserData($data, $id)
	 {

		if(!$this->getUserDataByUserId($id))
		{
			$sql = "INSERT INTO `users`
							(`firstName`, `lastName`, `email`)
							VALUES (:firstName, :lastName, :email)";				
		}
		else
		{
			$sql = "UPDATE `users` SET
						firstName = :firstName, lastName = :lastName, email = :email
						WHERE `id` = '$id'";
		}

		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':firstName', $data['firstName']);
		$stmt->bindParam(':lastName', $data['lastName']);
		$stmt->bindParam(':email', $data['mail']);
		$stmt->execute();
		
	}

	public function changeMain($newMain)
	{
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


	public function savePosition($id, $latLng, $country, $state, $city, $method)
	{
		if($method == "save")
		{
			$sql = "INSERT INTO `user_position` (`lat`, `lng`, `user_id`, `country`, `country_code`, `state`, `city`)
				VALUES (:lat, :lng, :user_id, :country, :country_code, :state, :city)";
				$stmt = $this->db->prepare($sql);
				print_r($latLng);
				$stmt->bindParam(':lat', $latLng['lat']);
				$stmt->bindParam(':lng', $latLng['lng']);		
				$stmt->bindParam(':user_id', $id);		
				$stmt->bindParam(':country', $country['country']);
				$stmt->bindParam(':country_code', $country['country_code']);
				$stmt->bindParam(':state', $state);
				$stmt->bindParam(':city', $city);
				$stmt->execute();		
		}
		else if($method == "update")
		{
			$sql = "UPDATE `user_position` SET lat = :lat, lng = :lng, country = :country, country_code = :country_code, state = :state, city = :city
			 WHERE `user_id` = '$id'";		
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':lat', $latLng['lat']);
			$stmt->bindParam(':lng', $latLng['lng']);		
			$stmt->bindParam(':country', $country['country']);
			$stmt->bindParam(':country_code', $country['country_code']);
			$stmt->bindParam(':state', $state);
			$stmt->bindParam(':city', $city);
			$stmt->execute();		
		}
	}


	
	public function checkAutoTrigger($id)
	{
		$sql = "SELECT `autoset` FROM `user_position` WHERE `user_id` = '$id'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		if(!$res)
			return 0;
		else
			return 1;
	}
	public function checkManTrigger($id)
	{
		$sql = "SELECT `manualset` FROM `user_position` WHERE `user_id` = '$id'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		if(!$res)
			return 0;
		else
			return 1;
	}
	public function checkifCoordsEqual($id, $lat, $lng)
	{
		$sql = "SELECT `lat` FROM `user_position` WHERE `user_id` = '$id' UNION SELECT `lng` FROM `user_position` WHERE `user_id` = '$id'"; 
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		if($res)
		{
			if($lat == $res[0]['lat'] && $lng == $res[1]['lat'])
			{
				return 1;
			}
			else if(empty($res))
			{
				return 0;
			}
			else
				return 2;
		}	
	}

	public function switchTrigger($id, $method)
	{
		if($method == "manual")
		{
			$sql = "UPDATE `user_position` SET `autoset` = 0 WHERE `user_id` = '$id'";
			$sql1 = "UPDATE `user_position` SET `manualset` = 1 WHERE `user_id` = '$id'";
		}
		else if($method == "auto")
		{
			$sql = "UPDATE `user_position` SET `manualset` = 0 WHERE `user_id` = '$id'";
			$sql1 = "UPDATE `user_position` SET `autoset` = 1 WHERE `user_id` = '$id'";
		}
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$stmt = $this->db->prepare($sql1);
		$stmt->execute();
	}


	public function get_mainPhoto($id)
	{
		$sql = "SELECT mainPhoto FROM user_profile WHERE id = '$id'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		$photo = $res['mainPhoto'];
		return $photo;
	}
	public function get_location($id)
	{
		$sql = "SELECT country, state, city FROM user_position WHERE id = '$id'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		return $res;
	}

	public function get_biography($id)
	{
		$sql = "SELECT biography FROM user_profile WHERE id = '$id'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		$res = $res['biography'];
		return $res;
	}

	public function checklogin_mail($name, $mail)
	{
		$sql = "SELECT * FROM users WHERE username = '$name' AND email = '$mail'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		return $res;
	}

	public function writeNewPassword($name,  $password)
	{
		$sql = "UPDATE `users` SET `password` = '$password' WHERE `username` = '$name'";
		$stmt = $this->db->prepare($sql);
		$res = $stmt->execute();
		return $res;
	}
	public function getHash($name)
	{
		$sql = "SELECT username FROM users WHERE username = '$name'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		$hash = $res['username'];
		
		return md5($hash);
	}
	public function get_blacklist($id)
	{
		$sql = "SELECT users.id, users.firstName, users.lastName, user_profile.mainPhoto FROM users
				INNER JOIN 
				`user_profile` ON user_id = users.id
				INNER JOIN `blocked` ON blocker_id = '$id' AND blocked_id = users.id";

		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		return($res);	
	}
	public function unblock($id, $blocker)
	{
		$sql = "DELETE FROM blocked WHERE blocked_id = '$id' AND blocker_id = '$blocker'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();	
	}

	public function is_filled($user_id){
		$sql = "SELECT `filled`
				FROM `user_profile`
				WHERE `user_id` = :user_id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();

		return $stmt->fetchColumn();
	}

	public function mark_profile_as_filled($user_id){
		$sql = "UPDATE `user_profile`
				SET `filled` = 1
				WHERE `user_id` = :user_id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();
	}	
}

?>