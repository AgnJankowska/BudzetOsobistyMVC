<?php 

namespace App;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Config;


class Mail {
	
	//metoda do wysyłania wiadomości email
	public static function send($to, $subject, $text, $html) {
		$mail = new PHPMailer(true);
		try {
			
			//gdy wszystko działa komentujemy tę linijkę
			//odpowiada ona za wyświetlenie komunikatów 
			//$mail->SMTPDebug = SMTP::DEBUG_SERVER;  
					
			$mail->isSMTP();
			$mail->Host       = Config::HOST_EMAIL;                 
			$mail->SMTPAuth   = true;  
			$mail->Username   = Config::USER_NAME_EMAIL;
			$mail->Password   = Config::PASSWORD_EMAIL; 
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
			$mail->Port       = 587;       

			//Recipients
			$mail->setFrom(Config::USER_NAME_EMAIL);
			$mail->addAddress($to);     //Add a recipient

			//Content
			$mail->isHTML(true); 
			$mail->Subject = $subject;
			$mail->Body    = $html;
			$mail->AltBody = $text;

			$mail->send();
			echo 'Message has been sent';
		} 
		
		catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}
	
}