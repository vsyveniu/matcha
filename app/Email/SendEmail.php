<?php

namespace App\Email;

class SendEmail {
	public static $from = 'matcha@gmail.com'; 

	public function verification ($request) {
		$message = '<html><body>';
		$message .= 'Hello <b>'.$request->getParam('username').'</b><br>';
		$message .= 'Welcome to Matcha'.'<br>';
		$message .= 'Please click on the link below to finish youre registration:'.'<br>';
		$message .= '<a href="http://localhost:8080/matcha/public/auth/email/confirm?username='.$request->getParam('username').'&hash='.md5(md5($request->getParam('username'))).'">Click here to confirm your email</a>';
		$message .= "</body></html>";
		$subject = 'Registration on matcha';
		$this->send($request->getParam('email'), $message, $subject);
	}

	public function visitedNotification($profile){
		$message = '<html><body>';
		$message .= 'Hello <b>'.$profile['firstName'].'</b><br>';
		$message .= 'Somebody wached youre profile.Go and see who is this'.'<br>';
		$message .= '<a href="http://localhost:8080/matcha/public">Matcha</a>';
		$subject = 'Vizited';
		$this->send($profile['email'], $message, $subject);
	}

	public function sendDislike() {
		
	}

	/*public function passwordRecovery($randomPass) {
		$this->message = 'Hello '.$this->uname.'<br>';
		$this->message .= 'Youre new password is '.$randomPass.'<br>';
		$this->subject = 'Password recovery on camgru';
		$this->send();
	}

	public function commet_message () {
		$this->message = 'Hi '.$this->uname.'</b><br>';
		$this->message .= 'You have new comment under youre photo';
		$this->subject = 'Comment under youre pfoto';
		$this->send();
	}*/

	private function send($email, $message, $subject) {
		$headers  = "Content-type: text/html; charset=windows-1251 \r\n"; 
		$headers .= "From: ".self::$from."\r\n"; 
		$headers .= "Reply-To: reply-to@example.com\r\n";
		mail('igor1988@i.ua', $subject, $message, $headers);
		//mail($email, $subject, $message, $headers);
	}
}

?>