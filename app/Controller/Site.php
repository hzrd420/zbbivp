<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller to show specific sites like home or about page
 */
class Site extends Base {
  /**
   * Show home page for logged in users
   *
   * @param Base $f3 The instance of F3
   */
  public function showHomePage(\Base $f3): void {
    // Render page:
    $f3->set('page.title', 'Startseite');
    $f3->set('page.content', 'html/site/home.html');
  } // showHomePage()
} // class