<?php
require_once 'app/classes/ConnectDb.php';

if(!isset($_GET['id']) || strlen($_GET['id']) > 60){
  $page = "error";
}else{
  $page = "ok";


  $secret = $_GET['id'];

  $instance = ConnectDb::getInstance();
  $pdo = $instance->getConnection();


  //delete a reservation id, if requested
  if(isset($_POST['deleteid']) && filter_var($_POST['deleteid'], FILTER_VALIDATE_INT)){
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

  //get the courses
  $stmt = $pdo->prepare(
    'SELECT d.ID, d.Corso, d.Mese, d.Data, p.Mail
    FROM prenotazioni p, accounts a, date_prenotabili d
    WHERE p.Mail = a.Mail AND p.DataID = d.ID AND a.Secret = ?'
  );
  $stmt->execute([$secret]);
  $affectedRows = $stmt->rowCount();
  if($affectedRows < 1) $page = "error";

  $mail = "";
  $mesi = ["gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre"];
  $table = "<table>";
  while($row = $stmt->fetch()){
    $mail = $row['Mail'];
    $month = $row['Mese'];
    $monthName = $mesi[$month-1];
    $table .= "<tr>
      <td>{$row['Corso']}</td>
      <td>{$row['Data']} $monthName</td>
      <td><a data-id=\"{$row['ID']}\" class=\"btn\">cancella</a></td>
      </tr>";
  }
  $table .= "</table>";

}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>prenotazione open day liceo banfi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <script async type="text/javascript" src="./js/prenotazioni.js" ></script>
    <link rel="stylesheet" href="./css/prenotazioni.css" >
    <link href="https://fonts.googleapis.com/css?family=Julius+Sans+One|Roboto&display=swap" rel="stylesheet"> 
  </head>
  <body>
    <div class="container">
      <h1>banfi <span class="in">in</span> diretta</h1>
      <?php if($page=="error"): ?>
      <h2>pagina non trovata</h2>
      <p>nessuna registrazione trovata per questo id</p>
      <div class="bottom-container">
        <a class="btn">aggiungi una prenotazione</a>
        <a href="index.php" class="link" >homepage</a>
      </div>
      <?php else: ?>
      <h2>prenotazioni effettuate: </h2>
<!--
      <table>
        <tr>
          <td>classico</td>
          <td>12 dicembre</td>
          <td><a class="btn">cancella</a></td>
        </tr>
        <tr>
          <td>scienze applicate</td>
          <td>12 dicembre</td>
          <td><a class="btn cancel">annulla</a><a class="btn confirm">conferma</a></td>
        </tr>
      </table>
-->

      <?php echo $table; ?>
      <div class="bottom-container">
        <a class="btn">aggiungi</a>
        <a href="index.php" class="link" >homepage</a>
        <a>mail associata: <?php echo $mail ?></a>
      </div>
      
      <?php endif; ?>
    </div>
    <noscript>
      <div class="noscript">
        <h1>per utilizzare questo sito é necessario javascript</h1>
        <p>segui <a><a href="https://www.enable-javascript.com/it/">questi</a> passaggi e ricarica la finestra</a></p>
      </div>
    </noscript>
  </body>
</html>


