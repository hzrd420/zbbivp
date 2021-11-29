<?php
declare(strict_types=1);
namespace Model;

class User extends Base {
  use \Validation\Traits\CortexTrait;
  public const USERNAME_MIN_LENGTH = 3;
  public const USERNAME_MAX_LENGTH = 250;
  public const PASSWORD_MIN_LENGTH = 5;
  public const PASSWORD_MAX_LENGTH = 250;
  public const FIRST_NAME_MIN_LENGTH = 1;
  public const FIRST_NAME_MAX_LENGTH = 100;
  public const SURNAME_MIN_LENGTH = 1;
  public const SURNAME_MAX_LENGTH = 100;

  protected $fieldConf = [
    'username' => [
      'type' => 'varchar(' . self::USERNAME_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'unique' => true,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::USERNAME_MIN_LENGTH . '|max_len,' . self::USERNAME_MAX_LENGTH . '|unique'
    ],
    'password' => [
      'type' => 'varchar(' . self::PASSWORD_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'validate' => 'required|min_len,' . self::PASSWORD_MIN_LENGTH . '|max_len,' . self::PASSWORD_MAX_LENGTH,
      'post_filter' => 'hashPassword'
    ],
    'firstName' => [
      'type' => 'varchar(' . self::FIRST_NAME_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::FIRST_NAME_MIN_LENGTH . '|max_len,' . self::FIRST_NAME_MAX_LENGTH
    ],
    'surname' => [
      'type' => 'varchar(' . self::SURNAME_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|min_len,' . self::SURNAME_MIN_LENGTH . '|max_len,' . self::SURNAME_MAX_LENGTH
    ],
    'is_admin' => [
      'type' => 'BIT',
      'nullable' => false,
      'default' => 0
    ],
    
    'securityTokens' => [
      'has-many' => ['\Model\SecurityToken', 'userId']
    ]
  ];
  protected $table = 'user';
  public $sortableFields = [
    'username' => 'username',
    'surname' => 'surname',
    'firstName' => 'firstName',
    'is_admin'  => 'is_admin'
  ];
} // class