<?php

namespace App\Socket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class BaseSocket implements MessageComponentInterface {
	public function onOpen(ConnectionInterface $conn) {
    }

    public function onMessage(ConnectionInterface $from, $msg) {
    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}

?>