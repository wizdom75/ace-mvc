<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/**
 * Class Mail
 * @package App
 *
 * PHP version 7.2
 */


class Mail
{
    /**
     * Send message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     *
     * @return mixed
     */
    public static function send($to, $subject, $text, $html)
    {

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            //$mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';                        // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'pmdera@gmail.com';                 // SMTP username
            $mail->Password = 'Wazfan2n';                           // SMTP password
            $mail->SMTPSecure = 'TLS';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('pmdera@gmail.com', 'Mailer');
            $mail->addAddress($to, 'Joe User');     // Add a recipient
            $mail->addAddress('peter@nurseplan.co.uk');               // Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
           // $mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
           // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            //Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $html; // HTML message
            $mail->AltBody = $text; // Plain text message

            $mail->send();
            echo 'Message has been sent <pre>',var_dump($mail),'</pre>';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }

    }
}