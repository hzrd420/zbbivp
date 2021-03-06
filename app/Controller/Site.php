<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller to show specific sites like home or about page
 */
class Site extends Base {
  /**
   * Get interested model for home page
   */
  public function __construct(\AuthenticationHelper $authentication, \Model\Interested $interested) {
    parent::__construct($authentication);
    $this->interested = $interested;
  } // constructor

  /**
   * Show home page for logged in users
   *
   * @param Base $f3 The instance of F3
   */
  public function showHomePage(\Base $f3): void {
    // Load lists:
    $f3->set(
      'page.costCommitmentReceivedList',
      $this->interested->find(['payerCostCommitment = ?', 'received'])
    );
    $f3->set(
      'page.costCommitmentNotReceivedList',
      $this->interested->find(['payerCostCommitment != ?', 'received'])
    );
    // Render page:
    $f3->set('page.title', 'Startseite');
    $f3->set('page.content', 'html/site/home.html');
  } // showHomePage()
} // class