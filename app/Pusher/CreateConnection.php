<?php

namespace App\Pusher;

use Pusher\Pusher;

class CreateConnection {
    private $key = 'fd0440e60019404539bf';
    private $app_id = '616439';
    private $secret = '0ef7fcfa104bbb18391b';
    private $cluster = "eu";
    private $chanel;
    private $event;
    private $data;

    public function chat_message($chanel_id, $data) {
    	$this->chanel = 'chat-'.$chanel_id;
    	$this->event = 'message';
    	$this->data = $data;

    	$this->send_message();
    }

    public function notification_message($chanel_id, $data) {
        $this->chanel = 'notification-message-'.$chanel_id;
        $this->event = 'notification';
        $this->data = $data;

        $this->send_message();
    }

    private function send_message(){
    	$options = array(
				'cluster' => $this->cluster,
			);
		 	$pusher = new Pusher(
				$this->key,
				$this->secret,
				$this->app_id,
				$options
		  	);
		$pusher->trigger($this->chanel, $this->event, $this->data);
    }
}

?>