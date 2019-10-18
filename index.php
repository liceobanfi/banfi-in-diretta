<?php
require_once 'app/classes/ConnectDb.php';

$instance = ConnectDb::getInstance();
$pdo = $instance->getConnection();


$stmt = $pdo->query('SELECT Descrizione, Anno from `configurazione` LIMIT 1');
/* while ($row = $stmt->fetch()) { */
/* } */
$row = $stmt->fetch();
$annoCorrente = htmlspecialchars($row['Anno']);
$descrizione = htmlspecialchars( $row['Descrizione']);

//generate tables
$mesi = ["gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre"];

$stmt = $pdo->query('SELECT * FROM date_prenotabili order by Corso, Mese');
$courseOut = "";
$datesOut = "";
$enpty = true;
$isFirstCourse = true;
$lastCourse = "";
$lastMonth = 0;
while ($row = $stmt->fetch()) {
  //sanitize data before ijection into the DOM
  $course = htmlspecialchars($row['Corso']);
  $month = filter_var($row['Mese'], FILTER_VALIDATE_INT);
  $date = filter_var($row['Data'], FILTER_VALIDATE_INT);
  $availableDays = $row['GiorniDisponibili'];

  //opening tags
  if($enpty){
    $enpty = false;
    $courseOut = '<ul class="course_grid">';
    $datesOut = '<ul class="course_grid">';
  }

  //courses
  if($course !== $lastCourse){
    $lastCourse = $course;
    $selected = "";
    if($isFirstCourse){
      $isFirstCourse = false;
      $selected = ' class = "selected"';
    }
    $courseOut .= "<li$selected><a>$course</a></li>";
  }

  //opening dates ul

  //dates li
  if($month !== $lastMonth){
    $lastMonth = $month;
    $monthName = $mesi[$month-1];
    $datesOut .= "<li class=\"month_label\"><p>$monthName</p></li>";
  }else{
    $disabled = $datesOut > 0? "" : ' class="disabled"';
    $datesOut .= "<li$disabled><a>$date</a></li>";
  }

  //closing dates ul

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
  </head>
  <body>
    <div class="container">
<!-- temporaneo TODO: migliorare design descrizione, e aggiungere titolo in css grid -->
<p><?php echo($descrizione);?></p>
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
            <li class="month_label" ><p>novembre</p></li>
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
        <!-- <div class="grid"> -->
        <div class="form_outer_line ">
          <div class="form_box">
            <p>corso selezionato:<br><span id="selection_status"></span></p>
            <input name="email" placeholder="Email" type="text" />
            <input name="cognome" placeholder="Cognome" type="text" />
            <input name="nome" placeholder="Nome" type="text" />
            <input name="comune" placeholder="Comune di provenienza" type="text" />
            <input name="scuola" placeholder="Scuola di provenienza" type="text" />
            <a class="btn">prenota</a>
            <p class="hide_in_grid">Hai gia effettuato una prenotazione? <a class="action toggle-box" >gestisci</a></p>
          </div>
          <div class="mail_box">
            <p>inserisci la mail utilizzata per la tua precedente registrazione</p>
            <input name="email" placeholder="Email" type="text" />
            <a class="btn">conferma</a>
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
    <noscript>
      <div class="noscript">
        <h1>per utilizzare questo sito é necessario javascript</h1>
        <p>segui <a><a href="https://www.enable-javascript.com/it/">questi</a> passaggi e ricarica la finestra</a></p>
      </div>
    </noscript>
  </body>
</html>


