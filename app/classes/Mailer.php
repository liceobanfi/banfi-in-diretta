<?php
require_once dirname(__FILE__).'/../libraries/PHPMailer/PHPMailer.php';

class Mailer{
  // Hold the class instance.
  private static $instance = null;
  private $mail;
  /**
   * private constructor where the mail class is istantiated.
   */
  private function __construct() {
    global PHPMailer;
    $this->mail = new PHPMailer(true);

    $this->mail->SMTPDebug = 2;
    $this->mail->isSMTP();
    $this->mail->Host       = 'smtp.gfg.com;';
    $this->mail->SMTPAuth   = true;
    $this->mail->Username   = 'user@gfg.com';
    $this->mail->Password   = 'password';
    $this->mail->SMTPSecure = 'tls';
    $this->mail->Port       = 587;
    $this->mail->setFrom('from@gfg.com', 'Name');
  }

  /**
   * constructor. Call this method to istantiate the class
   * @return object - the singleton object
   */
  public static function getInstance() {
    if(!self::$instance) {
      self::$instance = new Mailer();
    }
    return self::$instance;
  }

  public static function sendTemplate($mail, $template, $data){
    $this->mail->addAddress($mail);
    /* $this->mail->addAddress('receiver2@gfg.com', 'Name'); */
    $this->mail->isHTML(true);
    $this->mail->Subject = 'Subject';
    $this->mail->Body    = 'HTML message body in <b>bold</b> ';
    $this->mail->AltBody = 'Body in plain text for non-HTML mail clients';
    $this->mail->send();
  }
}
