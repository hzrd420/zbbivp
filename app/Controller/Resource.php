<?php
declare(strict_types=1);
namespace Controller;

/**
 * Abstract base class for almost same controllers to list, show, edit and delete resources with own cortex models
 */
Abstract class Resource extends Base {
  // Some data and strings:
  protected $model;
  /**
   * The actions for the specific elements (list and view templates)
   *
   * Array with 4 elements and following format:
   *
   * 0: Name (searched in lng.resource.<name> or lng.<resourceName>.actions.<name>
   *
   * 1: Alias of the named route
   *
   * 2: Array of alias parameters, (key = @token in route, value = property of cortex model)
   *
   * 3: array of conditions (key = property of cortex model (string), value = value that must present in the property (model[key] === value))
   * For no conditions, use empty array
   *
   * @var array
   */
  protected $uiActions = [];
  protected $hasFilter = false;
  protected $canExport = false;
  protected $permitNew = true;
  protected $resourceName = '';

  /**
   * Constructor
   *
   * Load the specific model, will be overwritten in inherited classes and use parent::__construct()
   * @param Model $model the model to use
   */
  public function __construct(\AuthenticationHelper $authentication, \Model\Base $model) {
    parent::__construct($authentication);
    $this->model = $model;
    $f3 = \Base::instance();
    $f3->set('page.resourceName', $this->resourceName);
    // Set page.hasFilter to specific value to allow templates to show filters if they exist
    $f3->set('page.hasFilter', $this->hasFilter);
    // Set property canExport to page templates to show or hide export buttons
    $f3->set('page.canExport', $this->canExport);
    // Show templates if current user can do specific actions:
    $access = \Access::instance();
    $subject = 'user';
    $f3->set(
      'page.permitNew',
      $this->permitNew && $access->granted('/*/edit*', $subject)
    );

    // Set up ui actions:
    if (!$f3->get('CLI')) {
      $actions = $this->uiActions;
      $this->uiActions = [];
      foreach ($actions as $action) {
        $name = $action[0];
        $route = $action[1];
        if (!$access->granted($f3->alias($route), $subject))
          continue;
        if (!$f3->exists('lng.' . $this->resourceName . '.actions.' . $name, $lng))
          if (!$f3->exists('lng.resource.' . $name, $lng))
            $lng = $name;
        $this->uiActions[] = [
          'name' => $lng,
          'class' => $name . 'Action', // for CSS styling
          'alias' => $route,
          'params' => $action[2],
          'conditions' => $action[3]
        ];
      } // foreach
      $f3->set('page.getUIActions', function(\Model\Base $item) use ($f3) {
        $actions = [];
        foreach ($this->uiActions as $action) {
          foreach ($action['conditions'] as $key => $value) {
            if (
              $key[0] == '!'
              && $item->getRaw(substr($key, 1)) == $value
            )
              continue 2;
            else if ($item->getRaw($key) != $value)
              continue 2;
          } // foreach
          $params = $action['params'];
          foreach ($params as $key => &$param)
            $param = $item->getRaw($param);
          $actions[] = [
            'name' => $action['name'],
            'route' => $f3->alias($action['alias'], $params),
            'class' => $action['class']
          ];
        } // foreach
        return $actions;
      });
    } // if
  } // constructor

  /**
   * List all resources
   * @param Base $f3 The instance of F3
   */
  public function list(\Base $f3): void {
    $page = \Pagination::findCurrentPage() - 1;
    $limit = $f3->get('pagination.limit');
    // Generate filter and options for Cortex model:
    $filter = $this->createListFilter($f3, $this->model);
    $options = $this->createListOptions($f3, $this->model);
    $this->loadLists($f3); // Load lists if method is used for the filter
    // Execute list hook to load additional data or change the model before loading it
    $this->listHook($f3, $this->model, $filter, $options);
    $resourceList = $this->model->paginate($page, $limit, $filter, $options);
    $f3->set('page.resourceList', $resourceList);

    // Generate page browser:
    $pages = new \Pagination($resourceList['total'], $resourceList['limit']);
    $pages->setTemplate('html/snippets/pageBrowser.html');
    $pages->setRouteKeyPrefix('page-');
    $pages->setRange($f3->get('pagination.range'));
    $f3->set('page.pageBrowser', $pages->serve());

    // Set list of sortable fields into the hive to allow generating the sorting selector
    $f3->set('page.sortableFields', $this->model->sortableFields);

    // Render page:
    $f3->copy('lng.' . $this->resourceName . '.list.title', 'page.title');
    if (file_exists($f3->get('UI') . 'html/' . $this->resourceName . '/listFrame.html'))
      $f3->set('page.content', 'html/' . $this->resourceName . '/listFrame.html');
    else
      $f3->set('page.content', 'html/snippets/list.html');
  } // list()

  /**
   * Overwrite this method for certain tasks before list is loaded
   * @param Base $f3 The instance of F3
   * @param Model $model The model to change
   * @param array|null $filter The created list filter
   * @param array|null $options The created list options
   */
  protected function listHook(\Base $f3, \Model\Base $model, ?array &$filter, ?array &$options): void {
    // Do nothing, overwrite in inherited controller if you need the hook
  } // listHook()

  /**
   * Load record with specific primary key in $this->model
   * @param string $primary The primary key to load
   * @return boolean load succeded or not
   */
  protected function loadRecord(string $primaryKey): bool {
    try {
      $this->model->reset();
      $this->model->load(['_id = :primary', ':primary' => $primaryKey]);
      return $this->model->valid();
    } catch (\Throwable $e) {
      $this->model->reset();
      return false;
    } // catch
  } // loadRecord()

  /**
   * Create the list filter from user specific input via REQUEST
   *
   * @param Base $f3 The instance of F3
   * @param Model $model The model to change
   * @return ?array Array with cortex specific filters
   */
  protected function createListFilter(\Base $f3, \Model\Base $model): ?array {
    // Get possible filter options from REQUEST
    $options = $this->getOptionFields($f3, 'filter_');
    if (is_null($options))
      return null;
    $f3->scrub($options); // Filter user values
    $filters = $this->getFilters($model, $options);
    // Build filters, merge with "and"
    return !count($filters) ? null : $model->mergeFilter($filters);
  } // createListFilter()

  /**
   * Filter models with specific input
   *
   * Add filters to array or use has / filter from cortex
   * @param $opts
   * @return array Array of cortex filters
   */
  protected function getFilters(\Model\Base $model, array $opts): array {
    return [];
  } // getFilters()

  /**
   * Get list of specific options from REQUEST
   *
   * Example: Get all filter options from list withouth leading "filter_"
   * @param Base $f3 The instance of f3
   * @param string $prefix The prefix string to search for
   * @return array option items without prefix, null for no found items
   */
  protected function getOptionFields(\Base $f3, string $prefix): ?array {
    if (!$f3->exists('REQUEST', $req))
      return null;
    $fields = $f3->extract($req, $prefix);
    foreach ($fields as $key => $value) {
      if ($value === '')
        unset($fields[$key]);
    } // foreach
    return count($fields) ? $fields : null;
  } // getOptionFields()

  /**
   * Create options for model find options
   *
   * Get sorting and sorting order from GET
   * @param returns array Options, null if no params in GET
   */
  protected function createListOptions(\Base $f3, \Model\Base $model): ?array {
    // Standard ordering:
    if (count($model->sortableFields))
      $orderBy = $model->sortableFields[0];
    else
      $orderBy = $this->model->getPrimary();
    $orderArg = 'ASC'; // Standard order argument
    if ($f3->exists('GET.sort', $sortBy)) {
      // Check if sort field is in sortable fields of model
      if ($sortBy !== '' && in_array($sortBy, $model->sortableFields))
        $orderBy = $sortBy;
      // Get sorting order:
      if ($f3->exists('GET.order', $sortingOrder) && $sortingOrder == 'DESC')
        $orderArg = 'DESC';
    } // if

    // If orderBy consists of multiple fields, add to all of them the desired flag (asc or desc):
    $orderByFields = $f3->split($orderBy);
    if (count($orderByFields)) {
      foreach ($orderByFields as &$field) {
        if (
          strpos($field, 'ASC') === false
          && strpos($field, 'DESC') === false
          )
        $field .= ' ' . $orderArg;
      } // foreach
      $orderBy = implode(', ', $orderByFields);
    } // if

    // Create options array for find:
    $options = [];
    $options['order'] = $orderBy;
    return count($options) ? $options : null;
  } // createListOptions()

  /**
   * Show a specific resource
   * @param Base $f3 The instance of F3
   * @param array $params The parameters of the specific route
   */
  public function show(\Base $f3, array $params): void {
    try {
      $id = $params['id'];
      $result = $this->loadRecord($id);
      if (!$result) // Model could not be loaded
        $f3->error(404);

      // Run show hook for certain tasks like permissions or loading additional data
      $this->showHook($f3, $this->model);

      $f3->set('page.' . $this->resourceName, $this->model);
      $f3->copy('lng.' . $this->resourceName . '.show.title', 'page.title');
      $f3->set('page.content', 'html/' . $this->resourceName . '/show.html');
    } catch (ControllerException $e) {
      \Flash::instance()->addMessage($e->getMessage(), 'danger');
      $f3->reroute($this->reroute);
    } // catch
  } // show()

  /**
   * Overwrite this method for certain tasks before it will be shown by the show function without errors
   * @param Base $f3 The instance of F3
   * @param Model $model The model to change
   */
  protected function showHook(\Base $f3, \Model\Base $model): void {
    // Do nothing, only to overwrite optional
  } // showHook()

  /**
   * Show the form to edit or add a Resource
   * @param Base $f3 The instance of F3
   */
  public function showEditForm(\Base $f3): void {
    try {
      // Save csrf token to prevent CSRF attacks:
      $this->saveCsrf();
      $this->loadLists($f3); // Load lists if method is used for select boxes
      // Execute hook function to load a specified record if there is such one
      $this->loadEditFormRecord($f3);

      // Load constants from model to show dynamic hints for the minimum and maximum field lengths
      $f3->set('page.modelConsts', $f3->constants($this->model));
      // Show form:
      $f3->copy('lng.' . $this->resourceName . '.edit.title', 'page.title');
      $f3->set('page.content',
        'html/' . $this->resourceName . '/editForm.html');
    } catch (ControllerException $e) {
      \Flash::instance()->addMessage($e->getMessage(), 'danger');
      $f3->reroute($this->reroute);
    } // catch
  } // showEditForm()

  /**
   * Load model to edit if id is set and populate POST with model values
   * @param Base $f3 The instance of F3
   */
  protected function loadEditFormRecord(\Base $f3): void {
    // If id to edit is given and no post values already there, load data to edit into POST to show it into the edit form:
    if (!$f3->exists('PARAMS.id', $id))
      return;

    if (!$this->loadRecord($id))
      throw new ControllerException($f3->get('lng.' . $this->resourceName . '.error.noSuchResource'));

    // If post isn't set already, copy values of loaded record to post to show the values in the edit form:
    if (!$this->checkPostFields())
      $this->model->copyto('POST');
  } // loadEditFormRecord()

  /**
   * Evaluate the edit/add form and edit or add the resource
   * @param Base $f3 The instance of F3
   */
  public function edit(\Base $f3): void {
    try {
      $this->checkCsrf();
      if (!$this->checkPostFields())
        throw new ControllerException($f3->get('lng.error.missingFields'));

      $f3->scrub($_POST);
      if ($f3->exists('PARAMS.id', $id)) {
        // Load allready existing resource to edit it:
        if (!$this->loadRecord($id))
          throw new ControllerException($f3->get('lng.' . $this->resourceName . '.error.noSuchResource'));
      } // if

      // Run before edit hook for certain tasks like permissions or loading additional data
      $this->beforeEditHook($f3, $this->model);

      // Start transaction and set fields into the model:
      $this->model->startTransaction();
      $this->model->defaults(true); // Useful for check boxes without values like booleans)
      $this->model->copyfrom('POST');
      // Run optional function with model as parameter, may be overwridden by class to change model individually
      $this->editHook($f3, $this->model);
      if (!$this->model->validate())
        throw new ValidationException($f3->get('validationError.text'));
      $result = $this->model->save();
      // Run optional function with model as parameter, may be overwridden by class to do something after the edit
      $this->afterEditHook($f3, $result);

      // Commit transaction:
      $this->model->commitTransaction();
      \Flash::instance()->addMessage($f3->get('lng.' . $this->resourceName . '.edit.success'), 'success');
      $this->rerouteToLast();
    } catch (ControllerException $e) {
      $this->model->rollbackTransaction();
      \Flash::instance()->addMessage($e->getMessage(), 'danger');
      $this->showEditForm($f3);
    } catch (ValidationException $e) {
      $this->model->rollbackTransaction();
      \Flash::instance()->addMessage($e->getMessage(), 'danger');
      $this->showEditForm($f3);
    } catch (\Model\InvalidForeignKeyException $e) {
      $this->model->rollbackTransaction();
      \Flash::instance()->addMessage($f3->get('lng.error.invalidForeignKey', $f3->get('lng.model.' . strtolower($this->resourceName) . '.' . $e->getMessage() . '.label')), 'danger');
      $this->showEditForm($f3);
    } catch (\PDOException $e) {
      $this->model->rollbackTransaction();
      \Flash::instance()->addMessage($f3->get('lng.error.databaseError', $e->errorInfo[2]), 'danger');
      $this->showEditForm($f3);
    } // catch
  } // edit()

  /**
   * Overwrite this method for certain tasks before model will be edited
   * @param Base $f3 The instance of F3
   * @param Model $model The model that will be edited
   */
  protected function beforeEditHook(\Base $f3, \Model\Base $model): void {
    // Do nothing, only to overwrite optional
  } // beforeEditHook()

  /**
   * Overwrite this method to change the model before it will be saved by the edit function without errors
   * @param Base $f3 The instance of F3
   * @param Model $model The model to change
   */
  protected function editHook(\Base $f3, \Model\Base $model): void {
    // Do nothing, only to overwrite optional
  } // editCallback()

  /**
   * Overwrite this method to do something after the resource is saved successfully
   * @param Base $f3 The instance of F3
   * @param Model $model The model to change
   */
  protected function afterEditHook(\Base $f3, \Model\Base $model) {
    // Do nothing, only to overwrite optional
  } // afterEditCallback()

  /**
   * Delete a resource
   *
   * Show delete form with get and delete with post
   * @param Base $f3 The instance of F3
   * @param array $params The parameters of the route
   */
  public function delete(\Base $f3, array $params): void {
    try {
      $id = $params['id'];
      $this->loadRecord($id);
      if ($this->model->dry())
        throw new ControllerException($f3->get('lng.' . $this->resourceName . '.error.noSuchResource'));

      // Run delete hook for certain tasks like permission checks
      $this->deleteHook($f3, $this->model);
      // If get, show delete form, otherwise check post and delete the resource
      if ($f3->get('VERB') == 'GET') {
        // Show form:
        $this->saveCsrf();
        $f3->copy('lng.' . $this->resourceName . '.delete.title', 'page.title');
        $f3->set('page.' . $this->resourceName, $this->model);
        $f3->set('page.content', 'html/' . $this->resourceName . '/deleteForm.html');
      } else if ($f3->exists('POST.decision', $decision) && $decision === 'true') {
        // Delete resource:
        $this->checkCsrf();
        $this->model->erase();
        \Flash::instance()->addMessage($f3->get('lng.' . $this->resourceName . '.delete.success'), 'success');
        $f3->reroute($this->reroute);
      } else {
        // Keep Resource:
        \Flash::instance()->addMessage($f3->get('lng.' . $this->resourceName . '.delete.canceled'), 'info');
        $f3->reroute($this->reroute);
      } // else
    } catch (ControllerException $e) {
      \Flash::instance()->addMessage($e->getMessage(), 'danger');
      $f3->reroute($this->reroute);
    } // catch
  } // delete()

  /**
   * Overwrite this method for certain tasks before deleting a resource
   * @param Base $f3 The instance of F3
   * @param Model $model The model to change
   */
  protected function deleteHook(\Base $f3, \Model\Base $model): void {
    // Do nothing, only to overwrite optional
  } // deleteHook()

  /**
   * Overwrite this method to load needed lists
   *
   * These lists are loaded in editForm and List view for filtering
   * Save all lists under global var "page" in the hive of F3
   * @param \Base $f3 The instance of F3
   */
  protected function loadLists(\Base $f3): void {
    // Do nothing, only to overwrite in sub classes
  } // loadLists()

  /**
   * Check for the necessary post fields
   *
   * Checks of all necessary fields are in the POST array
   * @return bool All fields in Post or not
   */
  protected function checkPostFields(): bool {
    $f3 = \Base::instance();
    foreach ($this->model->getNecessaryPostFields() as $field) {
      if (!$f3->exists('POST.' . $field, $value) || $value === '')
        return false;
    } // foreach
    return true;
  } // checkPostFields()
} // class