<?php
require_once 'app/classes/ConnectDb.php';

$instance = ConnectDb::getInstance();
$pdo = $instance->getConnection();

//used for numeric to string date conversion
$mesi = ["gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre"];

//render formhandler messages
$formError = "";
$formSuccess = "";
$msg = "";
if(isset($_GET['message'])){
  $message = htmlspecialchars($_GET['message']);
  switch($message){
    case 'form-data-error':
      $formError = "<p>non è stato possibile completare l'operazione per via di un errore interno.</p>";
      break;
    case 'form-selection-not-found':
      $formError = "<p>La data selezionata non è piu valida.</p>";
      break;
    case 'form-selection-full':
      $formError = "<p>La data selezionata non è piu disponibile. Potrebbe essere scaduta, o esser stata prenotata da un altro utente nei minuti precedenti</p>";
      break;
    case 'duplicated-form':
      $formError = "<p>Hai già prenotato questa data</p>";
      break;
    case 'registration-failed':
      $formError = "<p>non è stato possibile completare l'operazione per via di un errore interno.</p>";
      break;
    case 'success':
      if(isset($_GET['dateid'])){
        //get the configured success message
        $stmt = $pdo->query('SELECT MessaggioRegistrazioneCompletata from `configurazione`');
        $row = $stmt->fetch();
        $msg = $row['MessaggioRegistrazioneCompletata'];
        //get the correct data to visualize instead of the data id
        $formSuccess = "<p>corso: -- data: --</p>";
        $stmt = $pdo->prepare('SELECT * FROM date_prenotabili WHERE ID = ?');
        $stmt->execute([$_GET['dateid']]);
        $affectedRows = $stmt->rowCount();
        if($affectedRows > 0){
          $row = $stmt->fetch();
          $mese = $mesi[$row['Mese']-1];
          $formSuccess = "<p>{$row['Corso']} {$row['Data']} $mese </p>";
        }

      }
  }
}


//page configuration data
$stmt = $pdo->query('SELECT Descrizione, Anno from `configurazione`');
$row = $stmt->fetch();
$annoCorrente = htmlspecialchars($row['Anno']);
$descrizione = htmlspecialchars($row['Descrizione'], ENT_IGNORE);

//generate tables

$stmt = $pdo->query('SELECT * FROM date_prenotabili order by Corso, Mese, Data');
$courseOut = "";
$datesOut = "";
$enpty = true;//seen the typo, too lazy to rename. used everywhere.
$isFirstCourse = true;
$lastCourse = "";
$lastMonth = 0;
while ($row = $stmt->fetch()) {
  //sanitize data before ijection into the DOM
  $course = htmlspecialchars($row['Corso']);
  $month = filter_var($row['Mese'], FILTER_VALIDATE_INT);
  $date = filter_var($row['Data'], FILTER_VALIDATE_INT);
  $availableDays = filter_var($row['GiorniDisponibili'], FILTER_VALIDATE_INT);

  //opening tags
  if($enpty){
    $enpty = false;
    $courseOut = '<ul class="course_grid">';
    $datesOut = '';
  }

  //courses
  if($course !== $lastCourse){
    /* $lastCourse = $course; */
    $selected = "";
    if($isFirstCourse){
      /* $isFirstCourse = false; */
      $selected = ' class = "selected"';
    }
    $courseOut .= "<li$selected><a>$course</a></li>";
  }

  //opening dates ul
  if($course !== $lastCourse){
    $lastCourse = $course;
    $hide = "";
    if($isFirstCourse){
      $isFirstCourse = false;
    }else{
      $datesOut .= "</ul>";
      $hide = "hidden";
    }
    $datesOut .= "<ul class=\"dates_grid $hide\">";
  }

  //dates li
  //month
  if($month !== $lastMonth){
    $lastMonth = $month;
    $monthName = $mesi[$month-1];
    $datesOut .= "<li class=\"month_label\"><p>$monthName</p></li>";
  }
  //number
  if($availableDays > 0){
    $datesOut .= "<li data-month=\"$monthName\"><a>$date</a></li>";
  }

}
//closing tags
if(!$enpty){
  $courseOut .= "</ul>";
  $datesOut .= "</ul>";
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>prenotazione open day liceo banfi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <script async type="text/javascript" src="./js/index.js" ></script>
    <link rel="stylesheet" href="./css/index.css" >
    <link href="https://fonts.googleapis.com/css?family=Julius+Sans+One|Roboto&display=swap" rel="stylesheet"> 
  </head>
  <body>
    <div class="container">
<!-- temporaneo TODO: migliorare design descrizione, e aggiungere titolo in css grid -->
<p><?php echo $descrizione;?></p>
      <br>

      <div class="calendar_container">
        <div data-year="<?php echo($annoCorrente);?>" class="calendar_outer_line">
          <h2>seleziona un corso</h2>
          <?php echo $courseOut;?>
<!--
          <ul class="course_grid">
            <li class="selected"><a>scientifico</a></li>
            <li><a>classico</a></li>
            <li><a>scienze applicate</a></li>
          </ul>
-->

          <?php echo $datesOut;?>
<!--
          <ul class="dates_grid">
            <li data-month="0" class="month_label" ><p>novembre</p></li>
            <li><a>12</a></li>
            <li><a>13</a></li>
            <li class="disabled"><a>14</a></li>
            <li><a>15</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li class=""><a>16</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li class="month_label" ><p>dicembre</p></li>
            <li><a>2</a></li>
            <li><a>3</a></li>
            <li><a>4</a></li>
          </ul>
          <ul class="dates_grid hidden">
            <li class="month_label" ><p>novembre</p></li>
            <li><a>12</a></li>
            <li><a>13</a></li>
            <li class="disabled"><a>14</a></li>
            <li><a>15</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li class="month_label" ><p>dicembre</p></li>
            <li><a>2</a></li>
            <li><a>3</a></li>
            <li><a>4</a></li>
          </ul>
          <ul class="dates_grid hidden">
            <li class="month_label" ><p>novembre</p></li>
            <li><a>12</a></li>
            <li><a>13</a></li>
            <li class="disabled"><a>14</a></li>
            <li><a>15</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li class=""><a>16</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li><a>16</a></li>
            <li class="month_label" ><p>dicembre</p></li>
            <li><a>2</a></li>
            <li><a>3</a></li>
            <li><a>4</a></li>
          </ul>
-->
        </div>
      </div>

      <div class="form_container">
        <div class="form_outer_line ">
          <div class="form_box">
            <p>corso selezionato:<br><span id="selection_status"></span></p>
            <input name="email" placeholder="Email" type="text" />
            <input name="cognome" placeholder="Cognome" type="text" />
            <input name="nome" placeholder="Nome" type="text" />
            <input name="comune" placeholder="Comune di provenienza" type="text" />
            <input name="scuola" placeholder="Scuola di provenienza" type="text" />
            <a class="btn" id="js_prenota_btn" >prenota</a>
            <p class="hide_in_grid">Hai gia effettuato una prenotazione? <a class="action toggle-box" >gestisci</a></p>
          </div>
          <div class="mail_box">
            <p>inserisci la mail utilizzata per la tua precedente registrazione</p>
            <input name="email" placeholder="Email" type="text" />
            <a class="btn" id="js_conferm_btn">conferma</a>
            <p class="hide_in_grid">Vuoi aggiungere una prenotazione? <a class="action toggle-box" >aggiungi</a></p>
          </div>
        </div>
        <div class="form_back_panel">
          <div class="left_panel">
            <p>Vuoi aggiungere una prenotazione?</p>
            <a class="btn toggle-box">aggiungi</a>
          </div>
          <div class="right_panel">
            <p>Hai già effettuato una prenotazione?</p>
            <a class="btn toggle-box">gestisci</a>
          </div>
        </div>
      </div>
    </div>

    <div class="error_modal">
      <?php echo $formError; ?>
      <a class="close">X</a>
    </div>

    <div class="success_modal">
      <div class="modal_container">
        <div class="close_btn_wrapper">
          <a class="close_btn">X</a>
        </div>
        <h2>prenotazione avvenuta con successo</h2>
        <p><?php echo $msg; ?></p>
        <h3>la tua prenotazione:</h3>
        <?php echo $formSuccess; ?>
        <p>Per modificare o visualizzare tutte le prenotazioni effettuate, visita il link che è stato inviato al tuo indirizzo email.
        Se non hai ricevuto niente, controlla la cartella di spam e tieni a mente che la mail potrebbe impiegare alcuni minuti ad arrivare.</p>
      </div>
    </div>

    <noscript>
      <div class="noscript">
        <h1>per utilizzare questo sito é necessario javascript</h1>
        <p>segui <a><a href="https://www.enable-javascript.com/it/">questi</a> passaggi e ricarica la finestra</a></p>
      </div>
    </noscript>
  </body>
</html>


