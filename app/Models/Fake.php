<?php

namespace App\Models;

class Fake {
    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function mark($reporter, $blocked) {
        $sql = "INSERT INTO `fake` (`reporter_id`, `user_id`)
                VALUES (:reporter, :blocked)";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':reporter', $reporter);
        $stmt->bindParam(':blocked', $blocked);
        $stmt->execute();
    }

    public function exist($reporter, $blocked) {
        $sql = "SELECT count(*)
                FROM `fake`
                WHERE `reporter_id` = :reporter
                AND `user_id` = :blocked";
        $stmt = $this->container->db->prepare($sql);
        $stmt->bindParam(':reporter', $reporter);
        $stmt->bindParam(':blocked', $blocked);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}

?>