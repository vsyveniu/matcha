<?php

namespace App\Models;

class FameRating {
	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function update($user_id) {
		$id = $this->findFameId($user_id);
		if(!$id)
			$this->newUser($user_id);
		else if($this->getRating($user_id) < 2147483647) {
			$sql = "UPDATE `fame_rating` 
					SET `rating` = `rating` + 1
					WHERE `user_id` = :user_id";
			$stmt = $this->container->db->prepare($sql);
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
		}
	}

	public function getRating($user_id) {
		$sql = "SELECT `rating`
				FROM `fame_rating`
				WHERE `user_id` = :user_id";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();
		
		return $stmt->fetchColumn();
	}

	private function findFameId($user_id) {
		$sql = "SELECT `id`
				FROM `fame_rating`
				WHERE `user_id` = :user_id";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();

		return $stmt->fetchColumn();
	}

	public function newUser($user_id) {
		$sql = "INSERT INTO `fame_rating` (`user_id`)
				VALUES (:user_id)";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();
	}
}

?>