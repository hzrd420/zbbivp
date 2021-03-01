<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller for interested people
 */
class Interested extends Resource {
  protected $resourceName = 'interested';
  protected $uiActions = [
    ['edit', 'editInterested', ['id' => '_id'], ['isSoftErased' => false]],
    ['delete', 'deleteInterested', ['id' => '_id'], ['isSoftErased' => false]],
  ];
  protected $reroute = '@listInterested';
  protected $hasFilter = true;

  public function __construct(\Monolog\Logger $logger, \Authentication $authentication, \Model\Interested $model) {
    parent::__construct($logger, $authentication, $model);
  } // constructor

  protected function getFilters(\Model\Base $model, array $opts): array {
    $filters = [];
    // Training course 1:
    if (array_key_exists('trainingCourse1', $opts))
      $filters[] = ['trainingCourse1Id = ?', $opts['trainingCourse1']];

    // Training course 2:
    if (array_key_exists('trainingCourse2', $opts))
      $filters[] = ['trainingCourse2Id = ?', $opts['trainingCourse2']];

    // First name:
    if (array_key_exists('firstName', $opts))
      $filters[] = ['firstName LIKE ?', '%' . $opts['firstName'] . '%'];

    // Surname:
    if (array_key_exists('surname', $opts))
      $filters[] = ['surname LIKE ?', '%' . $opts['surname'] . '%'];

    // Address:
    if (array_key_exists('address', $opts))
      $filters[] = ['address LIKE ?', '%' . $opts['address'] . '%'];

    // Birth date:
    if (array_key_exists('birthDate', $opts))
      $filters[] = ['birthDate = ?', $opts['birthDate']];

    // Birth location:
    if (array_key_exists('birthLocation', $opts))
      $filters[] = ['birthLocation LIKE ?', '%' . $opts['birthLocation'] . '%'];

    // Email:
    if (array_key_exists('email', $opts))
      $filters[] = ['email LIKE ?', '%' . $opts['email'] . '%'];

    // Phone private:
    if (array_key_exists('phonePrivate', $opts))
      $filters[] = ['phonePrivate LIKE ?', '%' . $opts['phonePrivate'] . '%'];

    // Phone mobile:
    if (array_key_exists('phoneMobile', $opts))
      $filters[] = ['phoneMobile LIKE ?', '%' . $opts['phoneMobile'] . '%'];

    // Legal representative:
    if (array_key_exists('legalRepresentative', $opts))
      $filters[] = ['legalRepresentative LIKE ?', '%' . $opts['legalRepresentative'] . '%'];

    // Has childs:
    if (array_key_exists('hasChilds', $opts))
      $filters[] = ['hasChilds = ?', $opts['hasChilds'] === 'true' ? true : false];
    return $filters;
  } // getFilters()

  public function loadEditFormRecord(\Base $f3): void {
    // Check that there are already some trainingCourses:
    if ($this->model->rel('trainingCourse1Id')->count() === 0)
      throw new ControllerException($f3->get('lng.interested.error.noTrainingCourses'));
    parent::loadEditFormRecord($f3);
  } // showEditForm()

  protected function loadLists(\Base $f3): void {
    // Load training courses:
    $list = $this->model->rel('trainingCourse1Id');
    $f3->set('page.trainingCourseList', $list->find());
    // Load marital status list:
    $f3->set('page.maritalStatusList', $this->model->getMaritalStatusList());
  } // loadLists()
} // class