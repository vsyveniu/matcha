<?php

namespace App\Config;
use \PDO;

class Dbh{
	private $db;
	private $host;
	private $username;
	private $password;
	private $dbname;
	private $connection;

	public function connect($container) {
		$this->db = $container['settings']['db'];
		$this->host = $this->db['host'];
		$this->username = $this->db['username'];
		$this->password = $this->db['password'];
		$this->dbname = $this->db['dbname'];

		$this->connection = new PDO('mysql:host='.$this->host, $this->username, $this->password);
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		$sql = "CREATE DATABASE IF NOT EXISTS $this->dbname";
		try{
			$this->connection->exec($sql);
		} catch(PDOException $e) {
			return $e->getMessage();
		}

		$this->connection->exec("use $this->dbname");
		return $this->createTables($container);
	}

	public function createTables($container){
		$errors = array();

		$tables = [
			"users" => "CREATE TABLE IF NOT EXISTS `users` (
				`id` INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`username` VARCHAR(50) NOT NULL,
				`email` VARCHAR(100) NOT NULL,
				`password` VARCHAR(255) NOT NULL,
				`firstName` VARCHAR(255) NOT NULL,
				`lastName` VARCHAR(255) NOT NULL,
				`token` VARCHAR(128) NOT NULL DEFAULT '0',
				`verified` VARCHAR(1) NOT NULL DEFAULT 'N',
				`is_prefilled` int (1) DEFAULT 0,
				`lastActivity` TIMESTAMP
	      		)",

			"user_profile" => "CREATE TABLE IF NOT EXISTS `user_profile` (
				`id` INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`photos` TEXT,
				`mainPhoto` TEXT,
				`gender` TEXT,
				`sexualPreferences` VARCHAR(50),
				`biography` TEXT,
				`country` TEXT,
				`state` TEXT,
				`sity` TEXT,
				`dateOfBirth` DATE,
				`user_id` INT(11) NOT NULL,
				`filled` BIT DEFAULT 0,
				`language` VARCHAR(255),
				FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
	      	)",

	      	"blocked" => "CREATE TABLE IF NOT EXISTS `blocked` (
	      		`id` INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`blocked_id` INT (11) NOT NULL,
				`blocker_id` INT (11) NOT NULL
	      	)",

	      	"user_position" => "CREATE TABLE IF NOT EXISTS `user_position` (
				`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`lat` DECIMAL(10, 7),
				`lng` DECIMAL(10, 7),
				`user_id` INT(11) NOT NULL,
				`country` VARCHAR(255),
				`country_code` VARCHAR(255),
				`state` VARCHAR(255),
				`city` VARCHAR(255),
				`autoset` int(1) DEFAULT 1,
				`manualset` int(1) DEFAULT 0
	      	)",

			"countries" => "CREATE TABLE IF NOT EXISTS `countries` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`sortname` varchar(3) NOT NULL,
				`name` varchar(150) NOT NULL,
				`phonecode` int(11) NOT NULL,
				PRIMARY KEY (`id`)
				)ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=249",

			"states" => "CREATE TABLE IF NOT EXISTS `states` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(30) NOT NULL,
				`country_id` int(11) NOT NULL DEFAULT '1',
				 PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4121",

			"cities" => "CREATE TABLE IF NOT EXISTS `cities` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(30) NOT NULL,
				`state_id` int(11) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47577",

			"visits" => "CREATE TABLE IF NOT EXISTS `visits` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`visitor_id` int(11) NOT NULL,
				`visited_id` int(11) NOT NULL,
				`time` TIMESTAMP,
				PRIMARY KEY (`id`),
				FOREIGN KEY (visitor_id) REFERENCES users(id) ON DELETE CASCADE,
				FOREIGN KEY (visited_id) REFERENCES users(id) ON DELETE CASCADE
			)",

			"likes" => "CREATE TABLE IF NOT EXISTS `likes` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`like_id` int(11) NOT NULL,
				`liked_id` int(11) NOT NULL,
				`time` TIMESTAMP,
				PRIMARY KEY (`id`),
				FOREIGN KEY (like_id) REFERENCES users(id) ON DELETE CASCADE,
				FOREIGN KEY (liked_id) REFERENCES users(id) ON DELETE CASCADE
				)",

			"tags" => "CREATE TABLE IF NOT EXISTS `tags` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`tag` VARCHAR(255) NOT NULL,
				PRIMARY KEY (`id`)
			)",

			"users_tags" => "CREATE TABLE IF NOT EXISTS `users_tags` (
				`tag_id` int(11) NOT NULL,
				`user_id` int(11) NOT NULL,
				FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
			)",

			"fame_rating" => "CREATE TABLE IF NOT EXISTS `fame_rating` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`rating` int(11) NOT NULL NOT NULL DEFAULT '1',
				`user_id` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
			)",


			"notification_types" => "CREATE TABLE IF NOT EXISTS `notification_types` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`text` VARCHAR(255),
				PRIMARY KEY (`id`)
			)",

			"notifications" => "CREATE TABLE IF NOT EXISTS `notifications` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`user_id` int(11) NOT NULL,
				`user1_id` int(11) NOT NULL,
				`type` int(11) NOT NULL,
				`viewed` BIT DEFAULT 0,
				`time` TIMESTAMP,
				PRIMARY KEY (`id`),
				FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
				FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
				FOREIGN KEY (type) REFERENCES notification_types(id) ON DELETE CASCADE
			)",

			"chats" => "CREATE TABLE IF NOT EXISTS `chats` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`user_id` int(11) NOT NULL,
				`user1_id` int(11) NOT NULL,
				`active` BIT DEFAULT 1,
				`chanel` VARCHAR(50),
				`last_message` TIMESTAMP,
				PRIMARY KEY (`id`),
				FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
				FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE
			)",

			"messages" => "CREATE TABLE IF NOT EXISTS `messages` (
				`chat_id` int(11) NOT NULL,
				`sender_id` int(11) NOT NULL,
				`text` varchar(8000),
				`viewed` BIT DEFAULT 0,
				`time` TIMESTAMP,
				FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
				FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE
			)",

			"fake" => "CREATE TABLE IF NOT EXISTS `fake` (
				`reporter_id` int(11) NOT NULL,
				`user_id` int(11) NOT NULL,
				`time` TIMESTAMP,
				FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
				FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
			)",
		];

		foreach ($tables as $table => $sql) {
			try {
		        $this->connection->exec($sql);
			} catch (Exception $e) {
				$errors[] = $e->getMessage();
			}
		}

		$container->people->insertPeoples($this->connection);

		$insert_n_types = new Fill_notification_types($this->connection);
		$insert_n_types->fill();

		if (!$errors)
			return $this->connection;
		else
			return $errors;
	}
}

?>