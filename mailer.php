<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

// Enable SMTP debugging
// $mail->SMTPDebug = SMTP::DEBUG_SERVER;

// Set mailer to use SMTP
$mail->isSMTP();

// SMTP authentication
$mail->SMTPAuth = true;

// Gmail SMTP server settings
$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Gmail credentials
$mail->Username = "abhisuryanugroho0@gmail.com";  // Your Gmail address
$mail->Password = "zloy xclp lxma fhzu";  // Password application (not the Gmail password if 2FA is enabled)

// Set email format to HTML
$mail->isHtml(true);

return $mail;
