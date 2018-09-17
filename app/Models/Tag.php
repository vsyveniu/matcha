<?php

namespace App\Models;

class Tag {
	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function get_all_tags() {
		$sql = "SELECT DISTINCT `tag` 
				FROM `tags`
				ORDER BY `tag`";
		$stmt = $this->container->db->prepare($sql);
		$stmt->execute();

		return $stmt->fetchAll();
	}
	public function get_tags($user_id) {
		$sql = "SELECT tags.tag 
				FROM `tags`
				INNER JOIN `users_tags` ON tags.id = users_tags.tag_id
				WHERE users_tags.user_id = :user_id";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	public function save($tag, $user_id) {
		$tag_id = $this->getTagId($tag);
		if(!$tag_id) {
			$sql = "INSERT INTO `tags` (`tag`)
					VALUES (:tag)";
			$stmt = $this->container->db->prepare($sql);
			$stmt->bindParam(':tag', $tag);
			$stmt->execute();
			$tag_id = $this->container->db->lastInsertId();
		}

		if(!$this->userHaveTag($tag_id, $user_id)) {
			$sql = "INSERT INTO `users_tags` (`tag_id`, `user_id`)
					VALUES (:tag_id, :user_id)";
			$stmt = $this->container->db->prepare($sql);
			$stmt->bindParam(':tag_id', $tag_id);
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
		}
	}

	private function getTagId($tag) {
		$sql = "SELECT `id` FROM `tags`
				WHERE `tag` = :tag";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':tag', $tag);
		$stmt->execute();
		
		return $stmt->fetchColumn();
	}

	private function userHaveTag($tag_id, $user_id) {
		$sql = "SELECT `id` FROM `users_tags`
				WHERE `tag_id` = :tag_id
				AND `user_id` = :user_id
				LIMIT 1";
		$stmt = $this->container->db->prepare($sql);
		$stmt->bindParam(':tag_id', $tag_id);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();

		return $stmt->fetchColumn();
	}
}

?>