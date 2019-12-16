<?php

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use \Config\Config;
require '../vendor/autoload.php';

class Mail
{
	public static function send($emailTo, $subject, $body)
	{
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Host = Config::EMAIL_HOST;
		$mail->Port = Config::EMAIL_PORT;
		$mail->SMTPSecure = Config::EMAIL_SMTPSECURE;
		$mail->SMTPAuth = true;
		$mail->Username = Config::EMAIL_USERNAME;
		$mail->Password = Config::EMAIL_PASSWORD;
		$mail->setFrom(Config::EMAIL_USERNAME);
		$mail->addAddress($emailTo);;
		$mail->isHTML(true);
	    $mail->Subject = $subject;
	    $mail->Body = $body;
		if (!$mail->send())
		{
		    echo "Mailer Error: " . $mail->ErrorInfo;
		}
	}
}
