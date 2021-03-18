<?php
declare(strict_types=1);
namespace Model;

class TrainingCourse extends Base {
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
    'interested1' => [
      'has-many' => ['\Model\Interested', 'trainingCourse1Id']
    ],
    'interested2' => [
      'has-many' => ['\Model\Interested', 'trainingCourse2Id']
    ]
  ];
  protected $table = 'trainingCourse';
  public $sortableFields = [
    'name'
  ];
} // class