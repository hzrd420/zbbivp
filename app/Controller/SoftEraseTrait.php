<?php
declare(strict_types=1);
namespace Controller;

/**
 * Trait for resource controllers to restore soft erased resources
 */
trait SoftEraseTrait {
  /**
   * List soft erased resources
   * @param Base $f3 The instance of F3
   */
  public function listSoftErased(\Base $f3): void {
    // Show all soft erased models in collections
    $f3->set('CORTEX.showSoftErased', true);
    $limit = $f3->get('pagination.limit');
    $page = \Pagination::findCurrentPage() - 1;
    // Generate filter with overwritable method, useful for custom filters in special controllers
    $filter = $this->createListFilter($f3, $this->model);
    $options = $this->createListOptions($f3, $this->model);
    // Execute list hook to load additional data or change the model before loading it
    $this->listHook($f3, $this->model, $filter, $options);
    $resourceList = $this->model->paginateSoftErased($page, $limit, $filter, $options);
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
    $f3->copy('lng.' . $this->resourceName . '.list.titleSoftErased', 'page.title');
    $f3->copy('lng.' . $this->resourceName . '.list.headingSoftErased', 'lng.' . $this->resourceName . '.list.heading');
    if (file_exists($f3->get('UI') . 'html/' . $this->resourceName . '/listFrame.html'))
      $f3->set('page.content', 'html/' . $this->resourceName . '/listFrame.html');
    else
      $f3->set('page.content', 'html/snippets/list.html');
  } // listSoftErased()

  /**
   * Show a soft erased resource
   * @param Base $f3 The instance of F3
   * @param array $params The parameters of the specific route
   */
  public function showSoftErased(\Base $f3, array $params): void {
    try {
      // Show all soft erased models in collections
      $f3->set('CORTEX.showSoftErased', true);
      $resourceId = $params['id'];
      $resource = $this->model->findoneSoftErased(['_id = ?', $resourceId]);
      if (!$resource)
        $f3->error(404);

      // Run show hook for certain tasks like permissions or loading additional data
      $this->showHook($f3, $resource);
      $f3->set('page.' . $this->resourceName, $resource);
      $f3->copy('lng.' . $this->resourceName . '.show.titleSoftErased', 'page.title');
      $f3->copy('lng.' . $this->resourceName . '.show.headingSoftErased', 'lng.' . $this->resourceName . '.show.heading');
      $f3->set('page.content', 'html/' . $this->resourceName . '/show.html');
    } catch (ControllerException $e) {
      \Flash::instance()->addMessage($e->getMessage(), 'danger');
      $f3->reroute($this->reroute);
    } // catch
  } // showSoftErased()

  /**
   * Restore a soft erased resource
   * @param Base $f3 The instance of F3
   * @param array $params The parameters of the specific route
   */
  public function restoreSoftErased(\Base $f3, array $params): void {
    try {
      $f3->set('CORTEX.showSoftErased', true);
      $resourceId = $params['id'];
      $resource = $this->model->findoneSoftErased(['_id = ?', $resourceId]);
      if (!$resource)
        $f3->error(404);
      $this->model->startTransaction();
      $result = $resource->restore();
      if ($result) { // Restored
        \Flash::instance()->addMessage($f3->get('lng.' . $this->resourceName . '.restore.success'), 'success');
        $this->model->commitTransaction();
        $f3->reroute($this->reroute);
      } else { // Restoration failed
        \Flash::instance()->addMessage($f3->get('lng.' . $this->resourceName . '.restore.failed'), 'danger');
        $this->model->rollbackTransaction();
        $this->rerouteToLast();
      } // else
    } catch (ControllerException $e) {
      $this->model->rollbackTransaction();
      \Flash::instance()->addMessage($e->getMessage(), 'danger');
      $f3->rerouteToLast();
    } catch (\PDOException $e) {
      $this->model->rollbackTransaction();
      \Flash::instance()->addMessage($f3->get('lng.error.databaseError', $e->errorInfo[2]), 'danger');
      $f3->rerouteToLast();
    } // catch
  } // restoreSoftErased()

  /**
   * Delete a soft erased resource permanently
   * @param Base $f3 The instance of F3
   * @param array $params The parameters of the specific route
   */
  public function deleteSoftErased(\Base $f3, array $params): void {
    try {
      $f3->set('CORTEX.showSoftErased', true);
      $resourceId = $params['id'];
      $resource = $this->model->findoneSoftErased(['_id = ?', $resourceId]);
      if (!$resource)
        $f3->error(404);
      $this->model->startTransaction();
      $result = $resource->forceErase();
      if ($result) { // Deleted permanently
        \Flash::instance()->addMessage($f3->get('lng.' . $this->resourceName . '.delete.success'), 'success');
        $this->model->commitTransaction();
        $f3->reroute($this->reroute);
      } else { // Deletion failed
        \Flash::instance()->addMessage($f3->get('lng.' . $this->resourceName . '.delete.canceled'), 'danger');
        $this->model->rollbackTransaction();
        $this->rerouteToLast();
      } // else
    } catch (ControllerException $e) {
      $this->model->rollbackTransaction();
      \Flash::instance()->addMessage($e->getMessage(), 'danger');
      $f3->rerouteToLast();
    } catch (\PDOException $e) {
      $this->model->rollbackTransaction();
      \Flash::instance()->addMessage($f3->get('lng.error.databaseError', $e->errorInfo[2]), 'danger');
      $this->rerouteToLast();
    } // catch
  } // deletePermanently()
} // trait