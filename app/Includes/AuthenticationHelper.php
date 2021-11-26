<?php
declare(strict_types=1);
class AuthenticationHelper {
  protected $user;
  protected $token;

  /**
   * Initialize Authentication class
   */
  public function __construct(\Model\User $user, \Model\SecurityToken $token) {
    $this->user = $user;
    $this->token = $token;
  } // __construct

  /**
   * Try to login user
   * @param string username The username to search for
   * @param string $password The password of the user
   * @param bool $stayLoggedIn If true, set remember cookies, defaults to false
   * @return  bool Login successful
   */
  public function logInUser(string $username, string $password, bool $stayLoggedIn = false): bool {
    $f3 = \Base::instance();
    $this->user->load(['username = ?', $username]);
    if ($this->user->dry() || !password_verify($password, $this->user->password))
      return $this->failedLogin();
    // Log in user:
    $f3->set('SESSION.userId', $this->user->_id);
    if ($stayLoggedIn)
      $this->setRememberCookies($this->user->_id);
    // Erase old token of this user
    $this->token->eraseOldTokens($this->user->_id);
    return true;
  } // logInUser()

  /**
   * Call this method if a login is failed
   * Clear session and return false
   */
  protected function failedLogin(): bool {
    \Base::instance()->clear('SESSION');
    return false;
  } // failedLogin

  /**
   * Log out the user
   */
  public function logOutUser(): void {
    $f3 = \Base::instance();
    $f3->clear('SESSION');
    // Delete remember cookies if these exist:
    if ($f3->exists('COOKIE.identifier', $identifier) || $f3->exists('COOKIE.securitytoken', $securitytoken)) {
      $this->deleteRememberCookies();
      // Delete obsolete token from database
      $this->token->erase(['identifier = ?', $identifier]);
    } // if
    $this->user->reset();
  } // logoutUser()

  /**
   * Get logged in user or null if visitor isn't logged in
   * Use this to check if visitor of the page is logged in or not
   * @return object null if user isn't logged in, model with it's loaded record instead
   */
  public function getUser(): ?\Model\User {
    if ($this->user->valid())
      return $this->user;
    $f3 = \Base::instance();
    if (!$f3->exists('SESSION.userId', $userId)) {
      $userId = $this->checkRememberCookies();
      if (is_null($userId))
        return $userId;
      $f3->set('SESSION.userId', $userId);
    } // if

    // Search user in database:
    $this->user->load(['_id = ?', $userId]);
    if ($this->user->dry()) {
      // No user with this id
      $f3->clear('SESSION');
      return null;
    } // if
    return $this->user;
  } // getUser()

  

  /**
   * Check if "remember me" coockies exist
   * @ return int|null The userId of the user of the specific cookies or null if cookies are wrong
   */
  protected function checkRememberCookies(): ?int {
    $f3 = \Base::instance();
    if (!$f3->exists('COOKIE.identifier', $identifier) || !$f3->exists('COOKIE.securitytoken', $securitytoken))
      return null;
    // Search for token in database
    $this->token->load(['identifier = ?', $identifier]);
    // Check if token exists in database:
    if ($this->token->dry()) {
      // Delete identifier and security token:
      $this->deleteRememberCookies();
      return null;
    } // if

    // Token exists, check if tokens are identical:
    if (hash('sha512', $securitytoken) !== $this->token->token) {
      // Wrong token, Delete identifier and security token:
      $this->deleteRememberCookies();
      return null;
    } // if

    // Get user id specified with these cookies
    $userId = $this->token->userId->_id;
    // Set a new token
    $newSecurityToken = bin2hex(random_bytes(16));
    $this->token->token = hash('sha512', $newSecurityToken);
    $this->token->save();

    // Set new cookies:
    $tokenLifetime = \Base::instance()->get('authentication.tokenLifetime');
    $f3->set('COOKIE.identifier', $identifier, (3600*24*$tokenLifetime));
    $f3->set('COOKIE.securitytoken', $newSecurityToken, (3600*24*$tokenLifetime));

    // Return user id to log the specific user on
    return $userId;
  } // checkRememberCookies()

  /**
   * Create new identifier and security token and set the cookies for the "remember me" function
   */
  protected function setRememberCookies(int $userId): void {
    $f3 = \Base::instance();
    $identifier = bin2hex(random_bytes(16));
    $securityToken = bin2hex(random_bytes(16));
    $this->token->userId = $userId;
    $this->token->identifier = $identifier;
    $this->token->token = hash('sha512', $securityToken);
    $this->token->save();

    // Set new cookies:
    $tokenLifetime = $f3->get('authentication.tokenLifetime');
    $f3->set('COOKIE.identifier', $identifier, (3600*24*$tokenLifetime));
    $f3->set('COOKIE.securitytoken', $securityToken, (3600*24*$tokenLifetime));
  } // setRememberCookies()

  /**
   * Delete the remember cookies from the browser
   */
  protected function deleteRememberCookies(): void {
    $f3 = \Base::instance();
    $f3->clear('COOKIE.identifier');
    $f3->clear('COOKIE.securitytoken');
  } // deleteRememberCookies()
} // class