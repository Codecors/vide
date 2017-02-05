<?php 

class Email extends Main{
	
	const send_to = CONTACT_EMAIL;
	
	/**
	 * validates contact form input
	 * @param array $input
	 * @return array
	 */
	private function sanitize_input($input){
		
		$fname = filter_var($input['fname'], FILTER_VALIDATE_REGEXP, array("options" => 
				array("regexp" => "/[a-zA-Zäöüß \-]{2,20}/")
		));
		$lname = filter_var($input['lname'], FILTER_VALIDATE_REGEXP, array("options" => 
				array("regexp" => "/[a-zA-Zäöüß \-]{2,20}/")
		));
		$email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
		$about = filter_var($input['about'], FILTER_VALIDATE_REGEXP, array("options" => 
				array("regexp" => "/[a-zA-Z0-9_äöüß \-\.\:\&\,]{2,100}/")
		));
		$message = strip_tags($input['message']);
		
		$clean_output = array(
				'fname' => $fname,
				'lname' => $lname,
				'email' => $email,
				'about' => $about,
				'message' => $message
		);
		
		if(!$fname || !$lname || !$email || !$about){
			$this->add_error(__METHOD__, "ungültige Eingabe");
			return false;
		}else{
			return $clean_output;
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * Sends an email after contact form is submitted
	 * @param array @input
	 * @return boolean
	 */
	public function send_email($input){
		
		$sanitized = $this->sanitize_input($input);
		
		$mail_content = "Diese Nachricht wurde mit dem Kontaktformular auf [Website] gesendet.\r\n\r\nAbsender: " . $sanitized['fname'] . " " . $sanitized['lname'] . "\r\n\r\nAntwortadresse: " . $sanitized['email'] . "\r\n\r\nNachricht:\r\n\r\n" . $sanitized['message'];
		
		//testing on localhost
		$send = true;
// 		$send = mail($this::send_to, $sanitized['about'], $mail_content);
		
		if($send){
			$this->add_message("Vielen Dank, Ihre Nachricht wurde erfolgreich verschickt");
			$this->log_email($sanitized['about'], $mail_content);
			return true;
		}else{
			$this->add_error(__METHOD__, "Beim Versenden der Nachricht ist ein Fehler aufgetreten");
			return false;
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * Writes contact form emails to a log file
	 * @param string $sent_to Receipient address at time of sending
	 * @param string $subject
	 * @param string $content 
	 */
	private function log_email($subject, $content){
		
		$sent_to = $this::send_to;
		
		$time = date('d.m.Y H:i:s');
		
		$entry = "\r\n\r\n" . $time . " - " . $subject . "\r\n\r\nSent to: " . $sent_to . "\r\n\r\n" . $content . "\r\n\r\n---------------------------";
		
		$handle = fopen(CONTACT_LOG, 'a+');
		fwrite($handle, $entry);
		fclose($handle);
		
	}
	
	
	
} //end of class