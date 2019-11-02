<?php
require_once 'app/classes/ConnectDb.php';
require_once 'app/classes/Mailer.php';

//validates received output, and die if something fails
$error = "";
$expectedParams = [ 'course', 'month', 'number', 'email', 'nome', 'cognome', 'comune', 'scuola'];

foreach($expectedParams as $param){
  if(!isset($_POST[$param]) || strlen($_POST[$param]) > 60 ){
    $error .= " invalidPOST " . $param;
  }
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  $error .= "invalidMail ";
}

if($error) {
  /* header("HTTP/1.0 400 invalid data"); */
  /* echo $error; */
  header("Location: index.php?message=form-data-error");
  die();
}


$instance = ConnectDb::getInstance();
$pdo = $instance->getConnection();

//check if the selected course is valid
$stmt = $pdo->prepare(
  'select ID, GiorniDisponibili from date_prenotabili where Corso = :corso and Mese = :mese and Data = :data'
);

//get the numeric month
$months = ["gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre"];
$numericMonth = array_search($_POST['month'], $months) + 1;

$stmt->execute( [
  'corso' => $_POST['course'],
  'mese' => $numericMonth,
  'data' => $_POST['number']
]);
$row = $stmt->fetch();

if(!$row){
  header("Location: index.php?message=form-selection-not-found");
  die();
}
$giorniDisponibili = $row['GiorniDisponibili'];
$dateID = $row['ID'];

//check if the course is available
if($giorniDisponibili < 1){
  header("Location: index.php?message=form-selection-full");
  die();
}

//check if the user has alerady selected the same course
$stmt = $pdo->prepare(
  'SELECT * from prenotazioni WHERE DataID = :dateID and (
  Mail = :mail OR (Nome = :nome AND Cognome = :cognome)
  )'
);
$stmt->execute( [
  'dateID' => $dateID,
  'mail' => $_POST['email'],
  'nome' => $_POST['nome'],
  'cognome' => $_POST['cognome']
]);
$affectedRows = $stmt->rowCount();
if($affectedRows > 0){
  header("Location: index.php?message=duplicated-form");
  die();
}

//proceed to register the user
if (!empty($_SERVER['HTTP_CLIENT_IP'])){
  $ip = $_SERVER['HTTP_CLIENT_IP'];
}else{
  $ip = $_SERVER['REMOTE_ADDR'];
}
$timestamp = time();
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$stmt = $pdo->prepare(
  'INSERT INTO prenotazioni
 (Mail, Nome, Cognome, Comune, Scuola, Telefono, DataID, Ip, Timestamp, Useragent) VALUES
 (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$success = $stmt->execute( [
  $_POST['email'],
  htmlspecialchars($_POST['nome']),
  htmlspecialchars($_POST['cognome']),
  htmlspecialchars($_POST['comune']),
  htmlspecialchars($_POST['scuola']),
  0,
  $dateID,
  $ip,
  $timestamp,
  $userAgent
]);
if(!$success){
  header("Location: index.php?message=registration-failed");
  die();
}

//check if the user mail is in the accounts table, if its not, add it with an associated secret
$stmt = $pdo->prepare( 'SELECT * FROM accounts WHERE Mail = :mail');
$stmt->execute(['mail'=> $_POST['email'] ]);
$result = $stmt->rowCount();
if($result){
  //get secret
  $row = $stmt->fetch();
  $secret = $row['Secret'];
}else{
  //create secret, create db record
  $bytes = random_bytes(40);
  $secret = "BNF" . base64_encode($bytes);

  $stmt = $pdo->prepare( 'INSERT INTO accounts (Mail, Secret) VALUES (:mail, :secret)');
  $stmt->execute([
    'mail'=> $_POST['email'],
    'secret'=> $secret
  ]);
  $affectedRows = $stmt->rowCount();
  if($affectedRows !== 1){
    $error = "account-creation-error";
  }
}


//decrement the available places
$stmt = $pdo->prepare(
  'UPDATE date_prenotabili SET GiorniDisponibili = GiorniDisponibili - 1 
  WHERE GiorniDisponibili > 0 and Corso = :corso and Mese = :mese and Data = :data'
);
$stmt->execute( [
  'corso' => $_POST['course'],
  'mese' => $numericMonth,
  'data' => $_POST['number']
]);
$affectedRows = $stmt->rowCount();
if($affectedRows !== 1){
  $error = "available-days-decrement-error";
}


$infoCorso = htmlspecialchars($_POST['course']);
$numero = $_POST['number'];
$mese = $_POST['month'];
$infoCorso .= " il giorno $numero $mese";

//send the confirmation email
$mailData = [
  'secret' => $secret,
  'corso' => $_POST['course'],
  'numero' => $numero,
  'mese' => $mese
];
sendTemplate($_POST['email'], 'REGISTRATION_SUCCESS', $mailData);

header("Location: index.php?message=success&dateid=$dateID");
die();
