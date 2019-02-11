<?php

namespace App\Email;

class SendEmail {
	public static $from = 'matcha@gmail.com'; 

	public function verification ($request) {
		$message = '<html><body>';
		$message .= 'Hello <b>'.$request->getParam('username').'</b><br>';
		$message .= 'Welcome to Matcha'.'<br>';
		$message .= 'Please click on the link below to finish youre registration:'.'<br>';
		$message .= '<a href="http://localhost:8080/matcha/auth/email/confirm?username='.$request->getParam('username').'&hash='.md5(md5($request->getParam('username'))).'">Click here to confirm your email</a>';
		$message .= "</body></html>";
		$subject = 'Registration on matcha';
		$this->send($request->getParam('email'), $message, $subject);
	}

	public function reinit ($name, $mail, $password) {
		$message = '<html><body>';
		$message .= 'Hello <b>'.$name.'</b><br>';
		$message .= 'You forgot your password. What a shame.'.'<br>';
		$message .= 'Please click on the link below to restore your password:'.'<br>';
		$message .= '<a href="http://localhost:8080/matcha/auth/password/restore?username='.$name.'&hash='.md5($name).'&password='.password_hash($password, PASSWORD_BCRYPT).'">Click here to restore your password</a>';
		$message .= "</body></html>";
		$subject = 'Restoring password';
		$this->send($mail, $message, $subject);
	}



	public function visitedNotification($profile){
		$message = '<html><body>';
		$message .= 'Hello <b>'.$profile['firstName'].'</b><br>';
		$message .= 'Somebody wached youre profile.Go and see who is this'.'<br>';
		$message .= '<a href="http://localhost:8080/matcha/">Matcha</a>';
		$subject = 'Vizited';
		$this->send($profile['email'], $message, $subject);
	}

	public function sendDislike() {
		
	}

	private function send($email, $message, $subject) {
		$headers  = "Content-type: text/html; charset=windows-1251 \r\n"; 
		$headers .= "From: ".self::$from."\r\n"; 
		$headers .= "Reply-To: reply-to@example.com\r\n";
		mail($email, $subject, $message, $headers);
	}
}

?>