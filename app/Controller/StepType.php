<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller for step types
 */
class StepType extends Resource {
  protected $resourceName = 'stepType';
  protected $uiActions = [
    ['edit', 'editStepType', ['id' => '_id'], ['isSoftErased' => false]],
    ['delete', 'deleteStepType', ['id' => '_id'], ['isSoftErased' => false]],
  ];
  protected $reroute = '@listStepTypes';
  protected $hasFilter = true;

  public function __construct(\Monolog\Logger $logger, \Authentication $authentication, \Model\StepType $model) {
    parent::__construct($logger, $authentication, $model);
    // Set some counters
    // $this->model->countRel('steps');
  } // constructor

  protected function getFilters(\Model\Base $model, array $opts): array {
    $filters = [];
    // Name:
    if (array_key_exists('name', $opts))
      $filters[] = ['name LIKE ?', '%' . $opts['name'] . '%'];

    // Description:
    if (array_key_exists('description', $opts))
      $filters[] = ['description LIKE ?', '%' . $opts['description'] . '%'];

    // Means successful completion:
    if (array_key_exists('meansSuccessfulCompletion', $opts))
      $filters[] = ['meansSuccessfulCompletion = ?', $opts['meansSuccessfulCompletion'] === 'true'];

    return $filters;
  } // getFilters()

  protected function deleteHook(\Base $f3, \Model\Base $model): void {
    // Only delete if training course isn't already in use:
    // if (!is_null($model->count_interestedPeople) && $model->count_interestedPeople > 0)
      // throw new ControllerException($f3->get('lng.trainingCourse.error.alreadyInUse'));
  } // deleteHook()
} // class