<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller to administrate users
 */
class User extends Resource {
  protected $resourceName = 'user';
  protected $uiActions = [
    ['edit', 'editUser', ['id' => '_id'], []],
    ['delete', 'deleteUser', ['id' => '_id'], []]
  ];
  protected $hasFilter = true;
  protected $reroute = '@listUsers';

  public function __construct(\AuthenticationHelper $authentication, \Model\User $model) {
    parent::__construct($authentication, $model);
  } // constructor

  protected function getFilters(\Model\Base $model, array $opts): array {
    $filters = [];
    // username:
    if (array_key_exists('username', $opts))
      $filters[] = ['username LIKE ?', '%' . $opts['username'] . '%'];

    // First name:
    if (array_key_exists('firstName', $opts))
      $filters[] = ['firstName LIKE ?', '%' . $opts['firstName'] . '%'];

    // surname:
    if (array_key_exists('surname', $opts))
      $filters[] = ['surname LIKE ?', '%' . $opts['surname'] . '%'];

    return $filters;
  } // getFilters()

  protected function editHook(\Base $f3, \Model\Base $model): void {
    // Check if passwords matches:
    if ($f3->exists('POST.password', $password) && !empty($password)) {
      if (
        !$f3->exists('POST.password2', $password2)
        || $password !== $password2
      ) // Passwords do not match
        throw new ControllerException($f3->get('lng.user.error.wrongPasswords'));
      else // Passwords match, update in model
        $model->password = $password;
    } // if
  } // editHook()

  protected function deleteHook(\Base $f3, \Model\Base $model): void {
    // Prohibit deleting of last user:
    if ($this->model->count() === 1)
      throw new ControllerException($f3->get('lng.user.error.lastUser'));
  } // deleteHook()
} // class