<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
  public function sendMail(string $toEmail, string $toName, string $subject, string $body): bool
  {
    $mail = new PHPMailer(true);

    try {
      //Server settings
      $mail->isSMTP();                                            // Send using SMTP
      $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
      $mail->Username   = $_ENV['APP_EMAIL'];                     // SMTP username
      $mail->Password   = $_ENV['APP_PASSWORD'];                  // SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
      $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

      //Recipients
      $mail->setFrom('no-reply@rbxams.com', 'RBXAMS'); // Sender
      $mail->addAddress($toEmail, $toName);                       // Add a recipient

      // Content
      $mail->isHTML(true);                                        // Set email format to HTML
      $mail->Subject = $subject;
      $mail->Body    = $body;
      $mail->AltBody = strip_tags($body); // Plain text for non-HTML mail clients

      $mail->send();
      return true;
    } catch (Exception $e) {
      // Log the error or handle it appropriately
      error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
      return false;
    }
  }
}