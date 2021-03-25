<?php
declare(strict_types=1);
namespace Model;

class SecurityToken extends Base {
  use \Validation\Traits\CortexTrait;
  protected $fieldConf = [
    'identifier' => [
      'type' => \DB\SQL\Schema::DT_VARCHAR256,
      'nullable' => false
    ],
    'token' => [
      'type' => \DB\SQL\Schema::DT_VARCHAR256,
      'nullable' => false
    ],
    'created' => [
      'type' => \DB\SQL\Schema::DT_TIMESTAMP,
      'default' => \DB\SQL\Schema::DF_CURRENT_TIMESTAMP,
      'nullable' => false
    ],
    'userId' => [
      'belongs-to-one' => '\Model\User',
      'nullable' => false
    ]
  ];
  protected $table = 'securityToken';
  protected $db = 'db';

  /**
   * Delete old tokens of specific user
   *
   * All tokens older than configured token lifetime
   */
  public function eraseOldTokens($userId) {
    $lifetime = \Base::instance()->get('authentication.tokenLifetime');
    // Get maximum date:
    $date = new \DateTime();
    $date->sub(new \DateInterval('P' . $lifetime . 'D'));
    $lifetimeDate = $date->format('Y-m-d');
    $this->erase(['userId = ? and created < ?', $userId, $lifetimeDate]);
  } // eraseOldTokens()
} // class