<?php

/**
 * Cortextension
 *
 * An extended cortex Base model with additional functionality
 *
 * To use it, your models or another base model should extend this class
 *
 * Some of this work is based on the f3-softerase plugin at https://github.com/geofmureithi/f3-softerase
 *
 * Additional functions:
 * 
 * Soft erase
 * 
 * Replace empty strings for nullable with null
 *
 * Set timestamps for creation and last modification time on save / update / insert
 *
 *  The contents of this file are subject to the terms of the GNU General
 *  Public License Version 3.0. You may not use this file except in
 *  compliance with the license. Any of the license terms and conditions
 *  can be waived if you get permission from the copyright holder.
 *
 *  Copyright (c) 2020 by adkurz
 *  Adrian Kurz <adkurz@bltech.de>
 *  https://github.com/adkurz/f3-cse/
 *
 *  @package DB
 *  @version 0.2
 *  @date 2020-11-04
 */
abstract class Cortextension extends \DB\Cortex{
  // Set this var to a string (cortex field in $fieldConf) to enable soft erase feature
  protected $deleteField = null; //The Field to store the time of erasing.(Timestamp), use string to enable soft erase
  // If this var is true, all soft erased fields of this model and collections will be filtered
  public $filterSoftErased = true;
  // Disable this var to disable replacing empty strings with null for nullable fields
  protected $nullableEmptyStrings = true;
  // Use this vars to use the created and uppdated feature of Cortextension
  protected $createdField = null;
  protected $updatedField = null;

  /**
   * Get the (not Deleted) Filter.
   *
   * @return array|null
   */
  public function getNotDeletedFilter() {
    if (empty($this->deleteField))
      return null;
    return [$this->deleteField . ' = ?', null];
  } // getNotDeletedFilter()

  /**
   * Get the (Deleted) Filter.
   *
   * @return array|null
   */
  public function getDeletedFilter() {
    if (empty($this->deleteField))
      return null;
    return [$this->deleteField . ' != ?', null];
  } // getDeletedFilter()

  /**
   * Force a hard (normal) erase.
   *
   * @return bool
   */
  public function forceErase($filter = null) {
    return parent::erase($filter);
  } // forceErase()

  /**
   * Perform a soft erase.
   *
   * @return bool
   */
  public function erase($filter = NULL) {
    if (empty($this->deleteField))
      return parent::erase($filter);
    if (method_exists($this, 'beforeSoftErase')) {
      if (!$this->beforeSoftErase())
        return false;
    } // if
    $this->touch($this->deleteField);
    $result = $this->update() && !is_null($this->get($this->deleteField));
    if (method_exists($this, 'afterSoftErase'))
      $this->afterSoftErase();
    return $result;
  } // erase()

  /**
   * Little helper function to erase multiple records with specific filter
   *
   * Useful if before and after erase events are important
   *
   * Warning: Do not use with a large number of records, this would lead  to a high memory usage!
   *
   * @param Cortextension $model The model to erase records from
   * @param array $filter The filter to use
   * @param bool $forceErase Force erae or try soft erase
   */
  protected function eraseRecords(self $model, ?array $filter, $forceErase = false) {
    $records = $model->load($filter);
    while (!$model->dry()) {
      if ($forceErase && method_exists($record, 'forceErase'))
        $record->forceErase();
      else
        $model->erase();
      $model->next();
    } // while
  } // eraseRecords()

  /**
   * Little helper function to erase all records of a specific cortex collection
   * @param mixed $collection The collection to delete
   * @param bool $forceErase Force erase function
   */
  protected function eraseCollection($collection, $forceErase = false) {
    if (!is_null($collection) && $collection !== false) {
      foreach ($collection as $record) {
        if ($forceErase && method_exists($record, 'forceErase'))
          $record->forceErase();
        else
          $record->erase();
      } // foreach
    } // if
  } // eraseCollection()

  /**
   * Overwrite internal filtered find of cortex to load, find or count only not soft erased items
   * @param null  $filter
   * @param array $options
   * @param int   $ttl
   * @return mixed
   */
  protected function filteredFind($filter = null, array $options = null, $ttl = 0, $count = false) {
    if (
      empty($this->deleteField)
      || !$this->filterSoftErased
      || \Base::instance()->exists('CORTEX.showSoftErased', $globalFilterSetting)
      || $globalFilterSetting === true
    )
      return parent::filteredFind($filter, $options, $ttl, $count);
    if (!is_null($filter))
      return parent::filteredFind($this->mergeFilter([$this->getNotDeletedFilter(), $filter]), $options, $ttl, $count);
    return parent::filteredFind($this->getNotDeletedFilter(), $options, $ttl, $count);
  } // filteredFind()

  /**
   * Overwrite has of cortex to  include only not soft erased items
   * @param string $key
   * @param array $filter
   * @param null $options
   * @param array $options
   * @return $this
   */
  public function has($key, $filter, $options = null) {
    if ($this->filterSoftErased) {
      // Check if related model has soft erase features
      if (!isset($this->fieldConf[$key]))
        trigger_error(sprintf(self::E_UNKNOWN_FIELD,$key,get_called_class()),E_USER_ERROR);
      $model = $this->rel($key);
      if (method_exists($model, 'getNotDeletedFilter') && !is_null($model->getNotDeletedFilter())) {
        if (is_null($filter))
          $filter = $model->getNotDeletedFilter();
        else
          $filter = $this->mergeFilter([$model->getNotDeletedFilter(), $filter]);
      } // if
    } // if
    return parent::has($key, $filter, $options);
  } // has()

  /**
   * Overwrite countRel of cortex to  include only not soft erased items
   * @param string $key
   * @param string|null $alias
   * @param array $filter
   * @param null $options
   * @return $this
   */
  public function countRel($key, $alias = null, $filter = null, $option = null) {
    if ($this->filterSoftErased) {
      // Check if related model has soft erase features
      if (!isset($this->fieldConf[$key]))
        trigger_error(sprintf(self::E_UNKNOWN_FIELD,$key,get_called_class()),E_USER_ERROR);
      if (isset($this->fieldConf[$key]['relType'])) {
        $model = $this->rel($key);
        if (method_exists($model, 'getNotDeletedFilter') && !is_null($model->getNotDeletedFilter())) {
          if (is_null($filter))
            $filter = $model->getNotDeletedFilter();
          else
            $filter = $this->mergeFilter([$model->getNotDeletedFilter(), $filter]);
        } // if
      } // if
    } // if
    parent::countRel($key, $alias, $filter, $option);
  } // countRel()

  /**
   * Restore a soft-erased record.
   *
   * @return bool|null
   */
  public function restore() {
    if (empty($this->deleteField))
      return false;
    if (method_exists($this, 'beforeRestore')) {
      if (!$this->beforeRestore())
        return false;
    } // if
    $this->set($this->deleteField, null);
    $result = $this->update() && is_null($this->get($this->deleteField));
    if (method_exists($this, 'afterRestore'))
      $this->afterRestore();
    return $result;
  } // restore()

  /**
   * Ensure a record is inserted with the not erased state.
   *
   * @return bool|null
   */
  public function save() {
    if (!empty($this->deleteField))
      $this->set($this->deleteField, $this->get($this->deleteField) ? $this->get($this->deleteField) : null);
    return parent::save();
  } // save()

  /**
   * Determine if the active record has been soft-erased
   *
   * @return bool
   */
  public function isSoftErased() {
    return !empty($this->deleteField) && !is_null($this->get($this->deleteField));
  } // isSoftErased()

  /**
   * Load all soft erased records
   *
   * @return bool
   */
  public function loadSoftErased($filter = null, $options = null, $ttl = 0) {
    if (empty($this->deleteField))
      return false;
    if ($this->filterSoftErased)
      $switchFilter = true;
    $this->filterSoftErased = false;
    if (is_null($filter))
      $filter = $this->getDeletedFilter();
    else
      $filter = $this->mergeFilter([$this->getDeletedFilter(), $filter]);
    $result = $this->load($filter, $options, $ttl);
    if ($switchFilter)
      $this->filterSoftErased = true;
    return $result;
  } // loadSoftErased()

  /**
   * Find all soft erased records
   *
   * @return CortexCollection|false
   */
  public function findSoftErased($filter = null, $options = null, $ttl = 0) {
    if (empty($this->deleteField))
      return false;
    if ($this->filterSoftErased)
      $switchFilter = true;
    $this->filterSoftErased = false;
    if (is_null($filter))
      $filter = $this->getDeletedFilter();
    else
      $filter = $this->mergeFilter([$this->getDeletedFilter(), $filter]);
    $result = $this->find($filter, $options, $ttl);
    if ($switchFilter)
      $this->filterSoftErased = true;
    return $result;
  } // findSoftErased()

  /**
   * Find one soft erased record
   *
   * @return Cortex Model|false
   */
  public function findoneSoftErased($filter = null, $options = null, $ttl = 0) {
    if (empty($this->deleteField))
      return false;
    if ($this->filterSoftErased)
      $switchFilter = true;
    $this->filterSoftErased = false;
    if (is_null($filter))
      $filter = $this->getDeletedFilter();
    else
      $filter = $this->mergeFilter([$this->getDeletedFilter(), $filter]);
    $result = $this->findone($filter, $options, $ttl);
    if ($switchFilter)
      $this->filterSoftErased = true;
    return $result;
  } // findoneSoftErased()

  /**
   * Paginate over soft erased records
   */
  public function paginateSoftErased($pos = 0, $size = 10, $filter = null, array $options = null, $ttl = 0, $bounce = true) {
    if (empty($this->deleteField))
      return false;
    if ($this->filterSoftErased)
      $switchFilter = true;
    $this->filterSoftErased = false;
    if (is_null($filter))
      $filter = $this->getDeletedFilter();
    else
      $filter = $this->mergeFilter([$this->getDeletedFilter(), $filter]);
    $result = $this->paginate($pos, $size, $filter, $options, $ttl, $bounce);
    if ($switchFilter)
      $this->filterSoftErased = true;
    return $result;
  } // paginateSoftErased()

  /**
   * Overwrite function get to add specific fields
   * @return mixed
   * @param string $key
   * @param bool $raw
  */
  public function &get($key, $raw = false) {
    if ($key === 'isSoftErased')
      return $this->isSoftErased();
    return parent::get($key, $raw);
  } // &get()

  /**
   * Overwrite cortex function set to replace empty strings with null if field is nullable
   */
  public function set($key, $val) {
    if (
      $this->nullableEmptyStrings
      && isset($this->fieldConf[$key])
      && (!isset($this->fieldConf[$key]['nullable']) || (isset($this->fieldConf[$key]['nullable']) && $this->fieldConf[$key]['nullable'] == true))
      && is_string($val)
      && trim($val) === ''
    )
      $val = null;
    parent::set($key, $val);
  } // set()

  /**
   * Overwrite insert function to set created timestamp with creation date
   */
  public function insert() {
    if (!is_null($this->createdField) && array_key_exists($this->createdField, $this->fieldConf))
      $this->touch($this->createdField);
    return parent::insert();
  } // insert()

  /**
   * Overwrite update function to set last modified timestamp with creation date
   */
  public function update() {
    if (!is_null($this->updatedField) && array_key_exists($this->updatedField, $this->fieldConf))
      $this->touch($this->updatedField);
    return parent::update();
  } // update()
} // class