<?php
require_once 'app/classes/ConnectDb.php';
require_once 'app/classes/Mailer.php';

if(isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
  $instance = ConnectDb::getInstance();
  $pdo = $instance->getConnection();
  
  $stmt = $pdo->prepare( 'SELECT Secret FROM accounts WHERE Mail = :mail');
  $stmt->execute(['mail'=> $_POST['email'] ]);
  $result = $stmt->rowCount();
  if($result){
    //get secret
    $row = $stmt->fetch();
    $secret = $row['Secret'];
    $mailData = [
      'secret' => $secret
    ];
    sendTemplate($_POST['email'], 'ACCOUNT_URL', $mailData);
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>prenotazione open day liceo banfi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <script async type="text/javascript" src="./js/gestionemail.js" ></script>
    <link rel="stylesheet" href="./css/gestionemail.css" >
    <link href="https://fonts.googleapis.com/css?family=Julius+Sans+One|Roboto&display=swap" rel="stylesheet"> 
  </head>
  <body>
    <div class="container">
      <h2>controlla la tua casella di posta elettronica.</h2>
      <p>Per modificare o visualizzare tutte le prenotazioni effettuate, visita il link che è stato inviato al tuo indirizzo email. Se non hai ricevuto niente, controlla la cartella di spam e tieni a mente che la mail potrebbe impiegare alcuni minuti ad arrivare.</p>
      <a href="index.php" class="btn">indietro</a>
    </div>
  </body>
</html>
