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
    // 'interestedPeople' => [
      // 'has-many' => ['\Model\InterestedPeople', 'trainingCourseId']
    // ]
  ];
  protected $table = 'TrainingCourse';
  public $sortableFields = [
    'name' => 'name'
  ];

  /**
   * Get the translated value of the name from lng file
   * @param string $value the raw value
   * @return string The translated value
   */
  protected function get_name($value) {
    if (!\Base::instance()->exists('lng.training.trainingType.' . $value, $name))
      $name = $value;
    return $name;
  } // get_name()
} // class