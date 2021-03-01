<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller for training courses
 */
class TrainingCourse extends Resource {
  protected $resourceName = 'trainingCourse';
  protected $uiActions = [
    ['edit', 'editTrainingCourse', ['id' => '_id'], ['isSoftErased' => false]],
    ['delete', 'deleteTrainingCourse', ['id' => '_id'], ['isSoftErased' => false]],
  ];
  protected $reroute = '@listTrainingCourses';
  protected $hasFilter = true;

  public function __construct(\Monolog\Logger $logger, \Authentication $authentication, \Model\TrainingCourse $model) {
    parent::__construct($logger, $authentication, $model);
    // Set some counters
    $this->model->countRel('interested1');
    $this->model->countRel('interested2');
  } // constructor

  protected function getFilters(\Model\Base $model, array $opts): array {
    $filters = [];
    // name:
    if (array_key_exists('name', $opts))
      $filters[] = ['name LIKE ?', '%' . $opts['name'] . '%'];

    return $filters;
  } // getFilters()

  protected function deleteHook(\Base $f3, \Model\Base $model): void {
    // Only delete if training course isn't already in use:
    if (
      (!is_null($model->count_interested1) && $model->count_interested1 > 0)
      || (!is_null($model->count_interested2) && $model->count_interested2 > 0)
    )
      throw new ControllerException($f3->get('lng.trainingCourse.error.alreadyInUse'));
  } // deleteHook()
} // class