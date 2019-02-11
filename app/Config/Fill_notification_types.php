<?php

namespace App\Config;

Class Fill_notification_types {
    private $connection;
    private $types = ['liked you', 'viewed youre page', 'liked you back', 'unliked you', 'sent you a message'];

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function fill() {
        if(!$this->exist()){
            for($i = 0; $i < count($this->types); $i++) {
                $sql = "INSERT INTO `notification_types` (`text`) VALUES (?)";
                $this->connection->prepare($sql)->execute([$this->types[$i]]);
            }
        }    
    }
    private function exist() {
        $sql = "SELECT count(*) FROM `notification_types`";
        $res = $this->connection->prepare($sql);
        $res->execute();
        $count = $res->fetchColumn();

        return($count);
    }
}

?>