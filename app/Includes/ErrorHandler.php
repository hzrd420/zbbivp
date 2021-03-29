<?php
/**
 * Class to handle erros
 */
class ErrorHandler {
  /**
   * Handle the error
   */
  public function handle() {
    // Delete any previous output:
    while (ob_get_level())
      ob_end_clean();
    $f3 = \Base::instance();
    $errorCode = $f3->get('ERROR.code');
    $errorText = $f3->get('ERROR.text');

    if ($f3->get('AJAX'))
      exit(json_encode(['error' => ['text' => $errorText, 'code' => $errorCode]]));
    if ($f3->exists('lng.error.page.' . $errorCode, $error)) {
      $f3->set('page.title', $error['title']);
      $f3->set('page.error.text', $error['text']);
    } else {
      $f3->set('page.title', $f3->get('lng.error.page.error') . ' ' . $errorCode);
      $f3->set('page.error.text', $errorText);
    } // else
    $f3->set('page.content', 'html/snippets/error.html');
    echo(\Template::instance()->render('html/layouts/main.html'));
  } // handle()
} // class