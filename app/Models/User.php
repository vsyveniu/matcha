<?php

namespace App\Models;

class User{
	private $db;
	private $container;

	public function __construct($container){
		$this->db = $container->db;
		$this->container = $container;
	}

	public function create($request){
		$sql = "INSERT INTO `users`
				(`username`, `email`, `password`, `firstName`, `lastName`, `token`)
				VALUES (?, ?, ?, ?, ?, ?)";
		$stmt = $this->db->prepare($sql);
		try{
			$stmt->execute([$request->getParam('username'), $request->getParam('email'), password_hash($request->getParam('password'), PASSWORD_DEFAULT), $request->getParam('firstName'), $request->getParam('lastName'), '0']);
		} catch (PDOException $e) {
			return $e->getMessage();
		}
		$this->container->fame_rating->newUser($this->db->lastInsertId());

		return $stmt;
	}

	public function changePassword($request, $user){
		$sql = "UPDATE `users` SET `password` = :password WHERE `email` = :email";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':password', password_hash($request->getParam('password'), PASSWORD_DEFAULT));
		$stmt->bindParam(':email', $user['email']);

		return $stmt->execute();
	}

	public function findUserById($id){
		$sql = "SELECT * FROM `users` WHERE `id` = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':id' => $id));
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function getUserByEmail($email){
		$sql = "SELECT * FROM `users` WHERE `email` = :email";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':email' => $email));
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function getUserByUsername($username){
		$sql = "SELECT * FROM `users` WHERE `username` = :username";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':username' => $username));
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function emailVerifide($user){
		$sql = "UPDATE `users` SET `verified` = 'Y' WHERE `email` = :email";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':email', $user['email']);

		return $stmt->execute();
	}

	public function updateActivity() {
		$sql = "UPDATE `users` SET `lastActivity` = NOW() WHERE `id` = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':id', $_SESSION['user']);
		
		$stmt->execute();
	}
}

?>