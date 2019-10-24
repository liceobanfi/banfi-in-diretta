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

  //decrement the available places
  if($affectedRows > 0){
    $stmt = $pdo->prepare(
      'UPDATE date_prenotabili SET GiorniDisponibili = GiorniDisponibili + 1 
      WHERE ID = ?'
    );
    $stmt->execute( [$_POST['deleteid']]);
    $affectedRows = $stmt->rowCount();
    if($affectedRows !== 1){
      $error = "available-days-decrement-error";
    }
  }
  /* echo $affectedRows; */
}

header("location: prenotazioni.php?id=$secret&success=$affectedRows");
