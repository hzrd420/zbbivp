<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller for training courses
 */
class TrainingCourse extends Resource {
  protected $uiActions = [
    ['edit', 'editTrainingCourse', ['id' => '_id'], []],
    ['delete', 'deleteTrainingCourse', ['id' => '_id'], []],
  ];
  protected $reroute = '@listTrainingCourses';
  protected $hasFilter = true;

  public function __construct(\AuthenticationHelper $authentication, \Model\TrainingCourse $model) {
    parent::__construct($authentication, $model);
    // Set some counters
    $this->model->countRel('interested1');
    $this->model->countRel('interested2');
  } // constructor

  protected function getFilters(array $opts): array {
    $filters = [];
    // name:
    if (array_key_exists('name', $opts))
      $filters[] = ['name LIKE ?', '%' . $opts['name'] . '%'];

    return $filters;
  } // getFilters()

  protected function deleteHook(\Base $f3): void {
    // Only delete if training course isn't already in use:
    if (
      (!is_null($this->model->count_interested1) && $this->model->count_interested1 > 0)
      || (!is_null($this->model->count_interested2) && $this->model->count_interested2 > 0)
    )
      throw new ControllerException($f3->get('lng.trainingCourse.error.alreadyInUse'));
  } // deleteHook()
} // class