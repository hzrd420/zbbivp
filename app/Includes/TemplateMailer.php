<?php
declare(strict_types=1);

/**
 * Mail a f3-html template to a recipient
 */

class TemplateMailer {
  /**
   * Mail the template
   *
   * @param string $email the address of the recipient
   * @param string $subject the subject of the email to send
   * @param string $template the specific template to render and send
   * @return boolean mail is sended
   */
  public static function send(string $email, string $name, string $subject, string $htmlTemplate, ?string $textTemplate = null): void {
    $f3 = \Base::instance();
    // Validate email:
    if (!\Audit::instance()->email($email))
      throw new \Exception('Wrong email: ' . $email);

    // Check if templates exists:
    if (!file_exists($f3->get('UI') . $htmlTemplate))
      throw new \Exception('The specific html template "' . $htmlTemplate . '" doesn\'t exist.');
    else if (!is_null($textTemplate) && !file_exists($f3->get('UI') . $textTemplate))
      throw new \Exception('The specific plain text template "' . $textTemplate . '" doesn\'t exist.');

    $mail = new \Mailer();
    $mail->addTo($email);
    // Add html and text to the email:
    $htmlMessage = \Template::instance()->render($htmlTemplate);
    $mail->setHTML($htmlMessage);
    if (!is_null($textTemplate))
      $textMessage = \Template::instance()->render($textTemplate);
    else
      $textMessage = $f3->clean($message);
    $mail->setText($textMessage);
    // Send the email
    $mail->send($subject);
  } // send()
} // class