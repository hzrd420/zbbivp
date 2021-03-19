<?php
declare(strict_types=1);
namespace Controller;

/**
 * Abstract base class for all controllers
 *
 * Just a few basics
 */
abstract class Base {
  // Route to reroute on errors, overwrite in other controllers if necessary
  protected $reroute = '/';
  protected $authentication = null;

  /**
   * Initialize Base Controller
   * @param \Monolog\Logger $logger The logger
   */
  public function __construct(\AuthenticationHelper $authentication) {
    $this->authentication = $authentication;
  } // constructor

  /**
   * Function is called before routing to a new route
   *
   * Store last visited route, used for functions in the future
   * @param $f3 instance of f3
   */
  public function beforeroute(\Base $f3): void {
    // Add current route and last route to the session:
    if ($f3->exists('SESSION.currentRoute')) {
      if ($f3->get('SESSION.currentRoute') != $f3->get('PARAMS.0')) {
        $f3->copy('SESSION.currentRoute', 'SESSION.lastRoute');
      } // if
    } else {
      $f3->set('SESSION.lastRoute', '/');
    } // else
    $f3->set('SESSION.currentRoute', $f3->get('PARAMS.0'));
  } // beforeroute()

  /**
   * Function is called after the routing to a new route
   *
   * If is set, show the content of a f3-template
   * @param $f3 the instance of f3
   */
  public function afterroute(\Base $f3): void {
    // If the variable page.content exists, render the view:
    if ($f3->exists('page.content')) {
      echo(\Template::instance()->render('html/layouts/main.html'));
    } // if
  } // afterroute()

  protected function saveCsrf(): void {
    $f3 = \Base::instance();
    $f3->copy('CSRF', 'SESSION.csrf');
  } // saveCsrf()

  protected function checkCsrf(): void {
    $f3 = \Base::instance();
    $result = true;
    // CSRF token is sended in REQUEST as csrf
    if (!$f3->exists('SESSION.csrf', $sessionCsrf)
      || !$f3->exists('REQUEST.csrf', $requestCsrf))
      $result = false;
    else if ($sessionCsrf !== $requestCsrf)
      $result = false;

    if (!$result) {
      // Log out user and show error
      $this->logger->notice('Wrong CSRF token, logged out', ['username' => $this->authentication->getUser()->username]);
      $this->authentication->logOutUser();
      \Flash::instance()->addMessage($f3->get('lng.error.invalidCsrf'), 'danger');
      $f3->reroute('@login');
    } // if
  } // checkCsrf()

  /**
   * Route to last route
   *
   * If no last route or last route is the same like the current, use $this->reroute
   * @param Base $f3 The instance of f3
   */
  protected function rerouteToLast(): void {
    $f3 = \Base::instance();
    if (
      !$f3->exists('SESSION.lastRoute', $lastRoute)
      || empty($lastRoute)
      || $lastRoute == $f3->get('PARAMS.0')
    ) {
      $f3->reroute($this->reroute);
    } // if
    $f3->reroute($lastRoute);
  } // rerouteToLast()
} // class