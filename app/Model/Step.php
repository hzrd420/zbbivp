<?php
declare(strict_types=1);
namespace Model;

class Step extends Base {
  use \Validation\Traits\CortexTrait;

  protected $fieldConf = [
    'comment' => [
      'type' => \DB\SQL\Schema::DT_LONGTEXT,
      'accepted' => true,
      'filter' => 'trim'
    ],
    'interestedId' => [
      'belongs-to-one' => '\Model\Interested',
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true
    ],
    'stepTypeId' => [
      'belongs-to-one' => '\Model\StepType',
      'nullable' => false,
      'accepted' => true,
      'necessaryPost' => true
    ]
  ];
  protected $table = 'step';
} // class