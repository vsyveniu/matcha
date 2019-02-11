<?php

namespace App\Models;

class Blocked {
	private $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function is_blocked($user1_id, $user_id) {
		$sql = "SELECT count(*)
				FROM `blocked`
				WHERE `blocked_id` = '$user_id'
				AND `blocker_id` = '$user1_id'";
		$stmt = $this->container->db->prepare($sql);
		$stmt->execute();

		return $stmt->fetchColumn();
	}
}

?>