<?php

namespace App\Models;

class Chats {
    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function get_chats_list($uid) {

        $sql = "SELECT user_profile.mainPhoto,
                       user_profile.id,
                       (
                       SELECT `text`
                       FROM `messages`
                       WHERE `chat_id` = chats.id
                       ORDER BY `time`
                       LIMIT 1
                       ) AS `message`,
                       chats.chanel,
                       chats.last_message
                FROM `chats`
                INNER JOIN `user_profile`
                    ON
                    CASE
                        WHEN chats.user_id = :uid THEN chats.user1_id
                        ELSE chats.user_id
                    END = user_profile.id
                WHERE chats.active = 1
                    AND chats.user_id = :uid
                    OR chats.user1_id = :uid
                ORDER BY chats.last_message DESC"; 
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return  $stmt->fetchAll(\PDO::FETCH_ASSOC);                   
    }

    public function find_chat($uid1, $uid2) {
        $sql = "SELECT count(*)
                FROM `chats`
                WHERE (`user_id` = :uid1 AND `user1_id` = :uid2)
                    OR (`user_id` = :uid2 AND `user1_id` = :uid1)";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':uid1', $uid1);
        $stmt->bindParam(':uid2', $uid2);
        $stmt->execute();
        return  $stmt->fetchColumn();  
    }

    public function enable($uid1, $uid2) {

        $sql = "UPDATE `chats`
                SET `active` = 1
                WHERE (`user_id` = :uid1 AND `user1_id` = :uid2)
                    OR (`user_id` = :uid2 AND `user1_id` = :uid1)";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':uid1', $uid1);
        $stmt->bindParam(':uid2', $uid2);
        $stmt->execute(); 
    }

        public function disable($uid1, $uid2) {
        $sql = "UPDATE `chats`
                SET `active` = 0
                WHERE (`user_id` = :uid1 AND `user1_id` = :uid2)
                    OR (`user_id` = :uid2 AND `user1_id` = :uid1)";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':uid1', $uid1);
        $stmt->bindParam(':uid2', $uid2);
        $stmt->execute(); 
    }

    public function create($uid1, $uid2) {
        $chanel = md5(md5($uid1).md5($uid2));
        $sql = "INSERT INTO `chats` (`user_id`, `user1_id`, `chanel`)
                VALUES (:uid1, :uid2, :chanel)";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':uid1', $uid1);
        $stmt->bindParam(':uid2', $uid2);
        $stmt->bindParam(':chanel', $chanel);
        $stmt->execute(); 
    }

    public function find_all_chats($uid) {

        $sql = "SELECT `chanel`
                FROM `chats`
                WHERE `user_id` = :uid
                    OR `user1_id` = :uid
                    AND `active` = 1";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function get_chanel($uid1, $uid2) {
        $sql = "SELECT `chanel`
                FROM `chats`
                WHERE (`user_id` = :uid1 AND `user1_id` = :uid2)
                    OR (`user_id` = :uid2 AND `user1_id` = :uid1)";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':uid1', $uid1);
        $stmt->bindParam(':uid2', $uid2);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function get_user_photo($chat_chanel, $uid) {
        $sql = "SELECT user_profile.mainPhoto
                FROM `chats`
                INNER JOIN `user_profile`
                    ON
                    CASE
                        WHEN chats.user_id = :uid THEN chats.user1_id
                        ELSE chats.user_id
                    END = user_profile.id
                WHERE `chanel` = :chat_chanel";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':chat_chanel', $chat_chanel);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function get_users_id_by_chanel($chat_chanel) {
        $sql = "SELECT `user_id`, `user1_id`
                FROM `chats`
                WHERE `chanel` = :chat_chanel";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':chat_chanel', $chat_chanel);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function get_chat_by_chanel($chat_chanel) {
        $sql = "SELECT chats.id, `active`, `id`
                FROM `chats`
                WHERE `chanel` = :chat_chanel";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':chat_chanel', $chat_chanel);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function change_last_message_time($id, $last_message) {
        $sql = "UPDATE `chats`
                SET `last_message` = :last_message
                WHERE `id` = :id";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':last_message', $last_message);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

    }
}

?>