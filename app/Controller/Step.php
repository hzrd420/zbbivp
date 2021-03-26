<?php
declare(strict_types=1);
namespace Controller;
/**
 * Controller for step types
 */
class Step extends Resource {
  protected $resourceName = 'step';
  protected $uiActions = [
    ['edit', 'editStep', ['interestedId' => 'interestedId', 'id' => '_id'], []],
    ['finish', 'finishStep', ['id' => '_id'], ['!due' => null]],
    ['delete', 'deleteStep', ['id' => '_id'], []],
  ];
  protected $reroute = '@listInterested';

  public function __construct(\AuthenticationHelper $authentication, \Model\Step $model) {
    parent::__construct($authentication, $model);
  } // constructor

  protected function listHook(\Base $f3, \Model\Base $model, ?array &$filter, ?array &$options): void {
    $interestedId = $f3->get('PARAMS.interestedId');
    // Only show steps of specific interest:
    $interestedFilter = ['interestedId = ?', $interestedId];
    if (is_null($filter))
      $filter = $interestedFilter;
    else
      $filter = $model->mergeFilter([$filter, $interestedFilter]);

    // Load specific interested:
    $interested = $model->rel('interestedId');
    $interested = $interested->findone(['_id = ?', $interestedId]);
    if ($interested === false)
      throw new ControllerException($f3->get('lng.interested.error.noSuchResource'));
    $f3->set('page.interested', $interested);
  } // listHook()

  protected function loadEditFormRecord(\Base $f3): void {
    // Check that there are already some step types:
    if ($this->model->rel('stepTypeId')->count() === 0)
      throw new ControllerException($f3->get('lng.step.error.noStepTypes'));
    parent::loadEditFormRecord($f3);
    if ($this->model->dry()) {
      // Set id of NEW model to id in the route parameters
      $interestedId = $f3->get('PARAMS.interestedId');
      $this->model->interestedId = $interestedId;
      // Check that interested id exists:
      if ($this->model->rel('interestedId')->count(['_id = ?', $interestedId]) === 0)
        throw new ControllerException($f3->get('lng.interested.error.noSuchResource'));
      // If post is empty, copy model data to post (needed for interested id):
      if (!$this->checkPostFields())
        $this->model->copyto('POST');
    } // if
  } // loadEditFormRecord()

  protected function deleteHook(\Base $f3, \Model\Base $model): void {
    // Change reroute member to reroute to interested with deleted step
    $this->reroute = $f3->alias('listSteps', ['interestedId' => $model->getRaw('interestedId')]);
  } // deleteHook()

  protected function loadLists(\Base $f3): void {
    // Load step types:
    $list = $this->model->rel('stepTypeId');
    $f3->set('page.stepTypeList', $list->find());
  } // loadLists()

  /**
   * Finish a step
   *
   * Set due to null and show message
   */
  public function finish($f3, $params) {
    // Load step:
    $result = $this->loadRecord($params['id']);
    if (!$result) // Model could not be loaded
      throw new ControllerException($f3->get('lng.step.error.noSuchResource'));
    $this->model->due = null;
    $this->model->save();
    \Flash::instance()->addMessage($f3->get('lng.step.isFinished'), 'success');
    $this->rerouteToLast();
  } // finish()

  /**
   * Cron: Send reminders for due and over due steps
   */
  public function remind($f3) {
    $due = $this->model->findTodayDue();
    $overDue = $this->model->findOverDue();
    if ($due !== false) {
      $f3->set('page.dueSteps', $due);
      \TemplateMailer::send(
        $f3->get('mailer.email'),
        $f3->get('lng.mail.dueStepsTitle'),
        'email/dueSteps.html'
      );
    } // if

    if ($overDue !== false) {
      $f3->set('page.overDueSteps', $overDue);
      \TemplateMailer::send(
        $f3->get('mailer.email'),
        $f3->get('lng.mail.overDueStepsTitle'),
        'email/overDueSteps.html'
      );
    } // if
  echo 'Finished';
  } // remind()

  /**
   * List due steps (today)
   * @param Base $f3 The instance of F3
   */
  public function listDue(\Base $f3): void {
    $f3->set('page.list', $this->model->findDue());

    // Render page:
    $f3->copy('lng.step.list.dueTitle', 'page.title');
    $f3->copy('lng.step.list.dueHeading', 'page.heading');
    $f3->set('page.content', 'html/step/singleList.html');
  } // listDue()

  /**
   * List over due steps
   * @param Base $f3 The instance of F3
   */
  public function listOverDue(\Base $f3): void {
    $f3->set('page.list', $this->model->findOverDue());

    // Render page:
    $f3->copy('lng.step.list.overDueTitle', 'page.title');
    $f3->copy('lng.step.list.overDueHeading', 'page.heading');
    $f3->set('page.content', 'html/step/singleList.html');
  } // listOverDue()
} // class