<?php

namespace AutomatedRoster\Controllers;

use AutomatedRoster\Config\Connection;
use Exception;

/**
 * Duty Class
 */
class Duty
{
  use Validation;

  protected $connection = null;
  private $table = "duties";

  public function __construct()
  {
    $this->connection = new Connection();
  }

  /**
   * Create new duty
   *
   * @param array $request Form data request
   * @return bool
   **/
  public function createDuty(array $request): bool
  {
    extract($request);
    $duty = $this->validate_text($duty, "duty");
    $from = $this->validate_text($from, "from");
    $to = $this->validate_text($to, "to");
    $concentration_level = $this->validate_text($concentration_level, 'concentration level');

    // if no error
    if ($this->flag) {
      try {
        $queryStmt = "INSERT INTO {$this->table} (
          duty, period_from, period_to, concentration_level
        ) VALUES (?, ?, ?, ?)";
        $execStmt = $this->connection->insert(
          $queryStmt,
          ['sssi', $duty, $from, $to, $concentration_level]
        );

        // if true
        if ($execStmt) {
          $this->success[] = "New duty added successfully!";
          return true;
        }
      } catch (Exception $e) {
        print "An Error has occurred! Message: {$e->getMessage()}";
      }
    }
    return false;
  }

  /**
   * Fetch Duties
   * 
   * Method for fetching all available duties from the database
   * @param int $duty_id The ID of a particular duty from the DB
   * @return array
   */
  public function fetchDuty(int $duty_id): array
  {
    try {
      $result = [];
      $queryStmt = "SELECT * FROM {$this->table} WHERE duty_id = ?";
      $execStmt = $this->connection->select($queryStmt, ['i', $duty_id]);
      while ($row = $execStmt->fetch_assoc()) {
        $result[] = $row;
      }
    } catch (Exception $e) {
      print "An Error has occurred! Message: {$e->getMessage()}";
    }
    return $result[0] ?? [];
  }

  /**
   * Fetch Duties
   * 
   * Method for fetching all available duties from the database
   * @return array
   */
  public function fetchDuties(): array
  {
    try {
      $result = [];
      $queryStmt = "SELECT * FROM {$this->table}";
      $execStmt = $this->connection->select($queryStmt);
      while ($row = $execStmt->fetch_assoc()) {
        $result[] = $row;
      }
    } catch (Exception $e) {
      print "An Error has occurred! Message: {$e->getMessage()}";
    }
    return $result ?? [];
  }

  /**
   * Retrieve duties by concentration level
   *
   * @param int $concentration_level Duty post with concentration level
   * @param string $operator Conditional operator e.g '<', '>', '='
   * @return array
   * @throws Exception
   **/
  public function retrieveDutyByConcentrationLevel(
    int $concentration_level, 
    string $operator
    )
  {
    try {
      $result = [];
      $queryStmt = "SELECT * FROM {$this->table} WHERE concentration_level {$operator} {$concentration_level}";
      $execStmt = $this->connection->select($queryStmt);

      while ($row = $execStmt->fetch_assoc()) {
        $result[] = $row;
      }
    } catch (Exception $e) {
      print "An Error occured! Message: {$e->getMessage()}";
    }

    return $result;
  }

  /**
   * Update Duty
   *
   * @param array $request Form data request
   * @return bool
   **/
  public function updateDuty(array $request)
  {
    extract($request);
    $duty_id = $this->validate_number($duty_id);
    $duty = $this->validate_text($duty, "duty");
    $from = $this->validate_text($from, "from period");
    $to = $this->validate_text($to, "to period");
    $concentration_level = $this->validate_number($concentration_level, 'concentration level');

    // if no error
    if ($this->flag) {
      try {
        $queryStmt = "UPDATE {$this->table} SET
          duty = ?, period_from = ?, period_to = ?,
          concentration_level = ? WHERE duty_id = ?";
        $execStmt = $this->connection->update(
          $queryStmt,
          ['sssii', $duty, $from, $to, $concentration_level, $duty_id]
        );

        // if true
        if ($execStmt) {
          $this->success[] = "Duty updated successfully!";
          return true;
        }
      } catch (Exception $e) {
        print "An Error has occurred! Message: {$e->getMessage()}";
      }
    }
    return false;
  }

  /**
   * Delete duty
   *
   * @param array $request Form data request
   * @return bool
   **/
  public function deleteDuty(array $request)
  {
    extract($request);

    $duty_id = $this->validate_number($duty_id);
    if ($this->flag) {
      try {
        $queryStmt = "DELETE FROM {$this->table} WHERE duty_id = ?";
        $execStmt = $this->connection->delete($queryStmt, ['i', $duty_id]);
        if ($execStmt) {
          $this->success[] = "Duty deleted successfully!";
        }
      } catch (Exception $e) {
        print "An Error has occurred! Message: {$e->getMessage()}";
      }
    }
  }
}
