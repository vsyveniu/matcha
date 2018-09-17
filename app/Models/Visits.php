<?php

namespace App\Models;

class Visits{
	private $db;

	public function __construct($db){
		$this->db = $db;
	}

	public function save($visited_id){
		$visitor_id = $_SESSION['user'];

		$sql = "INSERT INTO `visits` (`visitor_id`, `visited_id`)
				VALUES (:visitor_id, :visited_id)";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':visitor_id', $visitor_id);
		$stmt->bindParam(':visited_id', $visited_id);

		$stmt->execute();
	}

	public function getByVisitorId($visitor_id, $start, $finish){
		$sql = "SELECT users.firstName, users.lastName, visits.id, visits.visited_id, visits.time
			FROM `visits`
			RIGHT  JOIN `users` ON users.id = visits.visited_id 
			WHERE `visitor_id` = :visitor_id
			ORDER BY visits.time
			DESC
			LIMIT :start, :finish";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':visitor_id', $visitor_id);
		$stmt->bindParam(':start', $start, \PDO::PARAM_INT);
		$stmt->bindParam(':finish', $finish, \PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt;
	}

	public function getByVisitedId($visited_id, $start, $finish){
		$sql = "SELECT users.firstName, users.lastName, visits.id, visits.visitor_id, visits.time
			FROM `visits`
			RIGHT  JOIN `users` ON users.id = visits.visitor_id 
			WHERE `visited_id` = :visited_id
			ORDER BY visits.time
			DESC
			LIMIT :start, :finish";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':visited_id', $visited_id);
		$stmt->bindParam(':start', $start, \PDO::PARAM_INT);
		$stmt->bindParam(':finish', $finish, \PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt;
	}
}

?>