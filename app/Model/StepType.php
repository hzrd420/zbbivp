<?php
declare(strict_types=1);
namespace Model;

class StepType extends Base {
  use \Validation\Traits\CortexTrait;
  public const NAME_MIN_LENGTH = 1;
  public const NAME_MAX_LENGTH = 200;

  protected $fieldConf = [
    'name' => [
      'type' => 'varchar(' . self::NAME_MAX_LENGTH . ')',
      'passThrough' => true,
      'nullable' => false,
      'unique' => true,
      'accepted' => true,
      'necessaryPost' => true,
      'filter' => 'trim',
      'validate' => 'required|unique|min_len,' . self::NAME_MIN_LENGTH . '|max_len,' . self::NAME_MAX_LENGTH
    ],
    'description' => [
      'type' => \DB\SQL\Schema::DT_LONGTEXT,
      'accepted' => true,
      'filter' => 'trim'
    ],
    'meansSuccessfulCompletion' => [
      'type' => \DB\SQL\Schema::DT_BOOLEAN,
      'default' => false,
      'nullable' => false,
      'accepted' => true
    ]
  ];
  protected $table = 'stepType';
  public $sortableFields = [
    'name' => 'name'
  ];
} // class