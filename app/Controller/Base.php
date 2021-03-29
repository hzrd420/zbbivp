<?php
declare(strict_types=1);
namespace Controller;

/**
 * Abstract base class for all controllers
 *
 * Just a few basics
 */
abstract class Base {
  // Route to reroute on errors and some actions, overwrite in other controllers if necessary
  protected $reroute = '/';

  protected $authentication = null;

  /**
   * Initialize Base Controller
   * @param \AuthenticationHelper $authentication A instance of the Authentication Helper class
   */
  public function __construct(\AuthenticationHelper $authentication) {
    $this->authentication = $authentication;
  } // constructor

  /**
   * Method is called before routing to a new route
   *
   * Store last visited route, used for method rerouteToLast
   * @param \Base $f3 Instance of f3
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
   * Method is called after the routing to a new route
   *
   * If is set, show the content of a f3-template
   * @param \Base $f3 The instance of f3
   */
  public function afterroute(\Base $f3): void {
    // If the variable page.content exists, render the view:
    if ($f3->exists('page.content'))
      echo(\Template::instance()->render('html/layouts/main.html'));
  } // afterroute()

  /**
   * Save the CSRF token in the session
   */
  protected function saveCsrf(): void {
    $f3 = \Base::instance();
    $f3->copy('CSRF', 'SESSION.csrf');
  } // saveCsrf()

  /**
   * Check that sended CSRF token is the same like the token in the session
   */
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
   * Route to last route stored in session
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