<?php

namespace App\Models;

class Likes{
	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function delete_like($liked_id, $like_id) {
		$sql = "DELETE FROM `likes`
				WHERE `like_id` = :like_id 
					AND `liked_id` = :liked_id";
		
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':like_id', $like_id);
		$stmt->bindParam(':liked_id', $liked_id);

		$stmt->execute();
	}

	public function insert_like($liked_id, $like_id) {
		$sql = "INSERT INTO `likes` (`like_id`, `liked_id`)
				VALUES (:like_id, :liked_id)";
		
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':like_id', $like_id);
		$stmt->bindParam(':liked_id', $liked_id);

		$stmt->execute();
	}

	public function findLike($liked_id, $like_id) {
		$sql = "SELECT * FROM `likes`
				WHERE `like_id` = :like_id 
				AND `liked_id` = :liked_id";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':like_id', $like_id);
		$stmt->bindParam(':liked_id', $liked_id);
		$stmt->execute();

		return ($stmt->rowCount());
	}

	public function getByLikeId($like_id, $start, $finish) {
		$sql = "SELECT users.firstName, users.lastName, likes.id, likes.liked_id, likes.time
				FROM `likes`
				RIGHT  JOIN `users` 
					ON users.id = likes.liked_id
				WHERE `like_id` = :like_id
				ORDER BY likes.time  DESC
				LIMIT {$start},{$finish}";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':like_id', $like_id);
		$stmt->execute();

		return $stmt;
	}

	public function getByLikedId($liked_id, $start, $finish) {
		$sql = "SELECT users.firstName, users.lastName, likes.id, likes.like_id, likes.time
				FROM `likes`
				RIGHT  JOIN `users` 
						ON users.id = likes.like_id 
				WHERE `liked_id` = :liked_id
				ORDER BY likes.time DESC
				LIMIT {$start},{$finish}";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':liked_id', $liked_id);
		$stmt->execute();
		
		return $stmt;
	}
}

?>