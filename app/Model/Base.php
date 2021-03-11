<?php
declare(strict_types=1);
namespace Model;

abstract class Base extends \DB\Cortex {
  // Disable this var to disable replacing empty strings with null for nullable fields
  protected $nullableEmptyStrings = true;
  // Use this vars to use the created and uppdated feature of the base model
  protected $createdField = null;
  protected $updatedField = null;

  protected $fieldConf = [];
  protected $table = '';
  protected $db = 'db';
  public $sortableFields = [];

  /**
   * Check for wrong foreign keys
   * @param $model The specific model
   * @param $id The specific id to search for
   * @param string $field The name of the specific field in $fieldconf
   * @returns The id to check
   * @throws InvalidForeignKeyException with specific field name as message
   */
  protected function checkForeignKey (self $model, $id, string $field): int {
    $model->load(['_id = ?', $id]);
    if ($model->dry())
      throw new InvalidForeignKeyException($field);
    return (int) $id;
  } // checkForeignKey()

  /**
   * Override copyfrom from cortex to use it with array of accepted fields in the specific models
   */
  public function copyfrom($key, $fields = null) {
    if (is_null($fields)) {
      $fields = $this->getAcceptedFields();
      if (!count($fields))
        throw new ModelException('No fields for copyfrom defined');
    } // if
    parent::copyfrom($key, $fields);
  } // copyfrom()

  /**
   * Get all necessary post fields
   * @return array Array of necessary field names as string
   */
  public function getNecessaryPostFields(): array {
    $fields = [];
    foreach ($this->fieldConf as $key => $value) {
      if (isset($value['necessaryPost']) && $value['necessaryPost'])
        $fields[] = $key;
    } // foreach
    return $fields;
  } // getNecessaryPostFields()

  /**
   * Get all accepted fields for copyfrom
   * @return array Array of accepted field names as string
   */
  protected function getAcceptedFields(): array {
    $fields = [];
    foreach ($this->fieldConf as $key => $value) {
      if (isset($value['accepted']) && $value['accepted'])
        $fields[] = $key;
    } // foreach
    return $fields;
  } // getAcceptedFields()

  /**
   * Start a transaction
   * @ return boolean transaction started
   */
  public function startTransaction(): bool {
    if ($this->dbtype() !== 'SQL' || $this->transactionRunning())
      return false;
    return $this->db->begin();
  } // startTransaction()

  /**
   * Commit transaction
   * @return Ended or not
   */
  public function commitTransaction(): bool {
    if ($this->dbtype() !== 'SQL' || !$this->db->trans())
      return false;
    return $this->db->commit();
  } // commitTransaction()

  /**
   * Rollback a transaction
   * @return boolean rolled back or not
   */
  public function rollbackTransaction(): bool {
    if ($this->dbtype() !== 'SQL' || !$this->db->trans())
      return false;
    return $this->db->rollback();
  } // rollbackTransaction()

  /**
   * Check if transaction is running
   * @return boolean transaction running or not
   */
  public function transactionRunning(): bool {
    return $this->db->trans();
  } // transactionRunning()

  /**
   * Get the primary key of the model
   * Useful for ordering by standard value
   */
  public function getPrimary(): string {
      return $this->primary;
  } // getPrimary()

  /**
   * Little helper method to erase multiple records with specific filter
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
      $model->erase();
      $model->next();
    } // while
  } // eraseRecords()

  /**
   * Little helper method to erase all records of a specific cortex collection
   * @param mixed $collection The collection to delete
   * @param bool $forceErase Force erase function
   */
  protected function eraseCollection($collection, $forceErase = false) {
    if (!is_null($collection) && $collection !== false) {
      foreach ($collection as $record) {
        $record->erase();
      } // foreach
    } // if
  } // eraseCollection()

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