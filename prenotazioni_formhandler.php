<?php
require_once 'app/classes/ConnectDb.php';


$affectedRows = 0;
$secret="";
//delete a reservation id, if requested
if(isset($_POST['deleteid']) && filter_var($_POST['deleteid'], FILTER_VALIDATE_INT)
  && isset($_POST['secret']) && strlen($_POST['secret']) < 60){

  $secret = $_POST['secret'];

  $instance = ConnectDb::getInstance();
  $pdo = $instance->getConnection();

  $stmt = $pdo->prepare('
    DELETE from prenotazioni WHERE DataID = ? AND Mail =
    (SELECT Mail FROM accounts WHERE Secret = ?)
  ');
  $stmt->execute([
    $_POST['deleteid'],
    $secret
  ]);

  $affectedRows = $stmt->rowCount();
  /* echo $affectedRows; */
}

header("location: prenotazioni.php?id=$secret&success=$affectedRows");
