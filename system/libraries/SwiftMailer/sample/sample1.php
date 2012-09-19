<?php
Sys::importLib('SwiftMailer', true);

$mailer  = Swift_Mailer::newInstance( Swift_MailTransport::newInstance() );
$message = Swift_Message::newInstance();

$message->setFrom("gabriel_dodan@yahoo.com", "Gabriel Dodan Yahoo");
$message->setTo("gabriel.dodan@gmail.com");
$message->setSubject("Test library");
$message->setBody("qwq<b>w</b>", 'text/html');
$mailer->send($message);
?>