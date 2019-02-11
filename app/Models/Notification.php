<?php

namespace App\Models;

Class Notification {
    private $container;

    public function __construct($container){
        $this->container = $container;
    }
    
    public function save($user_id, $user1_id, $type){
        $sql = "INSERT INTO `notifications` (`user_id`, `user1_id`, `type`, `time`) 
                VALUES (?,?,?,?)";
        $stmt = $this->container->db->prepare($sql);
        $stmt->execute([$user_id, $user1_id, $type, date("Y-m-d H:i:s")]);
    }

    public function getNotifications($user_id){
        $sql = "SELECT notifications.id, users.id as uid, users.firstName, users.lastName, notification_types.text, notifications.viewed, notifications.time
                FROM `notifications`
                INNER JOIN `users`
                    ON notifications.user1_id = users.id
                INNER JOIN `notification_types`
                    ON notifications.type = notification_types.id
                WHERE notifications.user_id = :user_id
                ORDER BY notifications.time DESC";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt;
    }

    public function getLastInsert($id){
        $sql = "SELECT notifications.id, users.id as uid, users.firstName, users.lastName, notification_types.text, notifications.viewed, notifications.time
                FROM `notifications`
                INNER JOIN `users`
                    ON notifications.user1_id = users.id
                INNER JOIN `notification_types`
                    ON notifications.type = notification_types.id
                WHERE notifications.id = :id";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt;
    }

    public function viewed($id){
        $sql = "UPDATE `notifications`
                SET `viewed` = 1
                WHERE `id` = :id";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function delete($id){
        $sql = "DELETE 
                FROM `notifications`
                WHERE `id` = :id";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function delete_all($id){

        $sql = "DELETE
                FROM `notifications` WHERE `user_id` = '$id'";
        $stmt = $this->container->db->prepare($sql);
        $stmt->execute();
    }

    public function count($user_id){
        $sql = "SELECT COUNT(*)
                FROM `notifications`
                WHERE `user_id` = :user_id
                AND `viewed` = 0";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}

?>