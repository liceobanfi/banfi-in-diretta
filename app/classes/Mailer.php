<?php
require_once dirname(__FILE__).'/../libraries/PHPMailer/PHPMailer.php';
require_once dirname(__FILE__).'/../libraries/PHPMailer/SMTP.php';
require_once dirname(__FILE__).'/../libraries/PHPMailer/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();

$mail->SMTPDebug = 2;
$mail->isSMTP();
$mail->Host       = 'smtp.gfg.com;';
$mail->SMTPAuth   = true;
$mail->Username   = 'user@gfg.com';
$mail->Password   = 'password';
$mail->SMTPSecure = 'tls';
$mail->Port       = 587;
$mail->setFrom('from@gfg.com', 'Name');

function sendTemplate($mail, $template, $data){
  $config = require dirname(__FILE__).'/../config/config.php';

  $mailBody = " test ";
  switch ($template){
  case 'REGISTRATION_SUCCESS':
    $url = $config['websitePath'] . "prenotazioni.php?id=" . $data['secret'];
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
    $url = $config['websitePath'] . "prenotazioni.php?id=" . $data['secret'];
    $mailBody =
<<<HTML
<h2>gestione prenotazioni</h2>
<p>per visualizzare o modificare le prenotazioni effettuate, clicca
<a href="$url">qui</a></p>
<p>se non hai effettuato nessuna registrazione, ignora questa mail</p>
<p>liceo banfi, etch etch</p>
HTML;
  }

  echo "mail che dovrebbe essere inviata:<br>" . $mailBody;

  /* $mail->addAddress($mail); */
  /* $mail->isHTML(true); */
  /* $mail->Subject = 'Subject'; */
  /* $mail->Body    = 'HTML message body in <b>bold</b> '; */
  /* $mail->AltBody = 'Body in plain text for non-HTML mail clients'; */
  /* $mail->send(); */
}
