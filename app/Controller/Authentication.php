<?php
declare(strict_types=1);
namespace Controller;

class Authentication extends Base {
  protected $authentication;

  /**
   * Initialize Controller
   * @param \Monolog\Logger $logger the logger
   * @param \Authentication The authentication object
   */
  public function __construct(\Monolog\Logger $logger, \AuthenticationHelper $authentication) {
    parent::__construct($logger, $authentication);
    $this->authentication = $authentication;
  } // constructor

  /**
   * Show the login page
   */
  public function showLoginPage(\Base $f3): void {
    if (!is_null($f3->get('USER')))
      $this->rerouteToLast();
    $f3->set('page.title', 'Anmelden');
    $f3->set('page.content', 'html/authentication/loginForm.html');
  } // showLoginPage()

  /**
   * Evaluate the sended login and log in the user if everything is ok
   * @Param $f3 instance of f3
   */
  public function logInUser(\Base $f3): void {
    sleep(1); // Against brute force attacks
    // Get data from post:
    if (!$f3->exists('POST.username', $username) || !$f3->exists('POST.password', $password)) {
      $this->showLoginPage($f3);
      return;
    } // if
    if (!$f3->exists('POST.stayLoggedIn', $stayLoggedIn))
      $stayLoggedIn = false;
    if ($this->authentication->logInUser($username, $password, (bool) $stayLoggedIn)) {
      // Success, Reroute to the home page
      if ($f3->exists('GET.origin', $origin))
        $f3->reroute($origin);
      else
        $this->rerouteToLast();
    } else {
      // Loggin not successful, show message and show login form again:
      \Flash::instance()->addMessage($f3->get('lng.authentication.wrongCredentials'), 'danger');
      $this->showLoginPage($f3);
    } // else
  } // logInUser()

  /**
   * Log out the user
   * @param $f3 the instance of f3
   */
  public function logout(\Base $f3): void {
    // Logout the user
    $this->authentication->logOutUser();
    \Flash::instance()->addMessage($f3->get('lng.authentication.loggedOut'), 'success');
    // Reroute to home page
    $f3->reroute('/');
  } // logout()
} // class