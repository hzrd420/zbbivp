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
  public static function send(string|array $emailTo, string $subject, string $htmlTemplate, ?string $textTemplate = null): void {
    $f3 = \Base::instance();
    // Check if templates exists:
    if (!file_exists($f3->get('UI') . $htmlTemplate))
      throw new \Exception('The specific html template "' . $htmlTemplate . '" doesn\'t exist.');
    else if (!is_null($textTemplate) && !file_exists($f3->get('UI') . $textTemplate))
      throw new \Exception('The specific plain text template "' . $textTemplate . '" doesn\'t exist.');

    $mail = new \Mailer();
    if (!is_array($emailTo))
      $emailTo = $f3->split($emailTo);
    foreach ($emailTo as $address) {
      // Validate email:
      if (!\Audit::instance()->email($address))
        throw new \Exception('Invalid email: ' . $address);
      $mail->addTo($address);
    } // foreach

    // Add html and text to the email:
    $htmlMessage = \Template::instance()->render($htmlTemplate);
    $mail->setHTML($htmlMessage);
    if (!is_null($textTemplate))
      $textMessage = \Template::instance()->render($textTemplate);
    else
      $textMessage = $f3->clean($htmlMessage);
    $mail->setText($textMessage);
    // Send the email
    $mail->send($subject);
  } // send()
} // class