<?php
require_once dirname(__FILE__).'/../libraries/PHPMailer/PHPMailer.php';
require_once dirname(__FILE__).'/../libraries/PHPMailer/SMTP.php';
require_once dirname(__FILE__).'/../libraries/PHPMailer/Exception.php';
$config = require dirname(__FILE__).'/../config/config.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();

/* $mail->SMTPDebug = 2; */
/* $mail->isSMTP(); */
/* $mail->Host       = 'smtp.gmail.com'; */
/* $mail->Username   = 'banfi.in.diretta@gmail.com'; */
/* $mail->setFrom('banfi.in.diretta@gmail.com', 'noreply.banfiindiretta'); */
/* $mail->Password   = ''; */
//this may be the cause of some issues, depending on the php environment
//https://stackoverflow.com/questions/23326934/phpmailer-smtp-connect-failed
/* $mail->SMTPSecure = 'tls'; */
/* $mail->SMTPAuth   = true; */
/* $mail->Port       = 587; */
$mail->CharSet = 'UTF-8';
if($config['smtpHost']) $mail->Host = $config['smtpHost'];
if($config['smtpUsername']) $mail->Username = $config['smtpUsername'];
if($config['smtpPassword']) $mail->Password = $config['smtpPassword'];
if($config['mailFrom']) $mail->setFrom($config['mailFrom'], $config['mailName']);

function sendTemplate($mailAddress, $template, $data){
  global $mail, $config;

  $mailBody = " test ";
  switch ($template){
  case 'REGISTRATION_SUCCESS':
    $url = $config['appPath'] . "prenotazioni.php?id=" . $data['secret'];
    $mailBody =
<<<HTML
<h2>registrazione avvenuta con successo</h2>
<p>Vi aspettiamo il giorno {$data['numero']} {$data['mese']} per
il corso {$data['corso']}</p>
<p>per visualizzare o modificare le prenotazioni effettuate, clicca
<a href="$url">qui</a></p>
<p>se non hai effettuato nessuna registrazione, ignora questa mail</p>
<p>liceo banfi, etch etch</p>
HTML;
  case 'ACCOUNT_URL':
    $url = $config['appPath'] . "prenotazioni.php?id=" . urlencode($data['secret']);
    $mailBody =
<<<HTML
<h2>gestione prenotazioni</h2>
<p>per visualizzare o modificare le prenotazioni effettuate, clicca
<a href="$url">qui</a></p>
<p>se non hai effettuato nessuna registrazione, ignora questa mail</p>
<p>liceo banfi, etch etch</p>
HTML;
  }

  /* echo "mail che dovrebbe essere inviata:<br>" . $mailBody; */

  $mail->addAddress($mailAddress);
  $mail->isHTML(true);
  $mail->Subject = 'Subject';
  $mail->Body    = 'HTML message body in <b>bold</b> ';
  $mail->AltBody = 'Body in plain text for non-HTML mail clients';
  $mail->send();
}
