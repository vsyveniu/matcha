<?php

namespace App\Models;

class ChatMessages {
	private $container;

	public function __construct($container){
		$this->container = $container;
	}

    public function get_chat_messages($chat_id){
        $sql = "SELECT `sender_id`, `text`, `time`
                FROM `messages`
                WHERE `chat_id` = :chat_id
                ORDER BY `time` ASC";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->execute();

        return  $stmt->fetchAll(\PDO::FETCH_ASSOC); 
    }

    public function save($message, $chat_id, $sender_id){
        $sql = "INSERT INTO `messages` (`chat_id`, `sender_id`, `text`)
                VALUES (:chat_id, :sender_id, :message)";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':sender_id', $sender_id);
        $stmt->execute();
    }

      public function get_last_mesasge_time($chat_id) {
        $sql = "SELECT `time` 
                FROM `messages`
                WHERE `chat_id` = :chat_id
                ORDER BY `time` DESC
                LIMIT 1";
         $stmt = $this->container->db->prepare($sql);
         $stmt->bindParam(':chat_id', $chat_id);
         $stmt->execute();

         return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}

?>