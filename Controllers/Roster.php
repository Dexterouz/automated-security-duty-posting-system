<?php
namespace AutomatedRoster\Controllers;

use AutomatedRoster\Config\Connection;
use Exception;

/**
 * Class for automating Roster
 */
class Roster extends Staff
{
  protected $connection = null;

  public function __construct() 
  {
    $this->connection = new Connection();
  }

  /**
   * Map Arrays
   * 
   * This method ensures that the available duties are map to 
   * respective staff randomly
   * @param array $staffs Array of all staffs
   * @param array $duties Array of all duties
   * @return array
   */
  public function mapArray(array $staffs, array $duties): array
  {
    $count = (count($staffs)) - (count($duties));
    $result = $duties;
    for ($i=0; $i < $count; $i++) { 
      shuffle($duties);
      array_push($result, $duties[$i]);
    }

    return $result ?? [];
  }

  /**
   * Current Day
   * 
   * Method for checking whether a new roster 
   * has been added for a new day or not
   * @return bool
   */
  public function currentDay(): bool
  {
    // get the current date & month of newly assign roster
    // from the rosters table
    $queryStmt = "SELECT date_created AS dates FROM rosters";
    $execStmt = $this->connection->select($queryStmt);

    $days = [];
    $flag = false;
    $current_day = date('d.m.Y');

    // retrieve dates from the db
    while ($row = $execStmt->fetch_assoc()) {
      $days[] = date('d.m.Y', strtotime($row['dates']));
    }

    if (!in_array($current_day, $days)) {
      $flag = true;
    }

    return $flag;
  }

  public function assignRoster()
  {
    $flag = false;
    $status = "absent";
    $duties_at_most_4_level = [];
    $duties_at_least_4_level = [];
    $staffs_at_most_4yrs = $this->retrieveStaffByExperience(4, '>=');
    $staffs_at_least_4yrs = $this->retrieveStaffByExperience(4, '<');

    foreach ($this->retrieveDutyByConcentrationLevel(4, '>=') as $data) {
      $duties_at_most_4_level[] = $data['duty_id'];
    }

    foreach ($this->retrieveDutyByConcentrationLevel(4, '<') as $data) {
      $duties_at_least_4_level[] = $data['duty_id'];
    }
    
    $at_most_4yrs = $this->mapArray($staffs_at_most_4yrs, $duties_at_most_4_level);
    $at_least_4yrs = $this->mapArray($staffs_at_least_4yrs, $duties_at_least_4_level);

    shuffle($at_most_4yrs);
    if ($this->currentDay()) {
      if (!empty($at_most_4yrs) && !empty($staffs_at_most_4yrs)) {
        for ($i=0; $i < count($at_most_4yrs); $i++) { 
          $queryStmt = "INSERT INTO rosters(
            staff_id, duty_id, attendance_code,
            status, date_created
          )
          VALUES (?, ?, ?, ?, NOW())";
          $execStmt = $this->connection->insert(
            $queryStmt, 
            ['iiis', $staffs_at_most_4yrs[$i]['staff_id'], $at_most_4yrs[$i], rand(30000, 90000), $status]
          );
        }
      }
      
      shuffle($at_least_4yrs);
      if (!empty($at_least_4yrs) && !empty($staffs_at_least_4yrs)) {
        for ($i=0; $i < count($at_least_4yrs); $i++) { 
          $queryStmt = "INSERT INTO rosters(
            staff_id, duty_id, attendance_code,
            status, date_created
          )
          VALUES (?, ?, ?, ?, NOW())";
          $execStmt = $this->connection->insert(
            $queryStmt, 
            ['iiis', $staffs_at_least_4yrs[$i]['staff_id'], $at_least_4yrs[$i], rand(30000, 90000), $status]
          );
        }
      }
    }
  }

  /**
   * Assign
   *
   * @param array $staffs Array of staff to be assign a role
   * @param array $duties Array of duties to be assign to staffs
   * @return bool
   * @throws Exception
   **/
  public function assign(array $staffs, array $duties)
  {
    $status = "absent";
    try {
      $assign = $this->mapArray($staffs, $duties);
      shuffle($assign);
      for ($i=0; $i < count($assign); $i++) { 
        $queryStmt = "INSERT INTO rosters(
          staff_id, duty_id, attendance_code,
          status, date_created
        )
        VALUES (?, ?, ?, ?, NOW())";
        $execStmt = $this->connection->insert(
          $queryStmt, 
          ['iiis', $staffs[$i]['staff_id'], $assign[$i], rand(30000, 90000), $status]
        );
        if ($execStmt) {
          return true;
        }
      }
    } catch (Exception $e) {
      print "An Error occurred! Message: {$e->getMessage()}";
    }

    return false;
  }

  /**
   * Mark Attendance
   *
   * Takes records of staff on-duty
   *
   * @param array $request Form data request
   * @return bool
   **/
  public function markAttendance(array $request)
  {
    extract($request);
    $staff_id = $this->validate_text($staff_id);
    $attendance_code = $this->validate_number($attendance_code, "attendance code");
    $status = "present";

    if ($this->flag) {
      $queryStmt = "SELECT staff_id FROM rosters WHERE staff_id = ? 
      AND attendance_code = ? AND date_created =  DATE(CURDATE())";

      $execStmt = $this->connection->select(
        $queryStmt, 
        ['ii', $staff_id, $attendance_code]
      );

      if ($execStmt->num_rows == 1) {
        // update attendance status
        $queryStmt = "UPDATE rosters SET status = ? WHERE staff_id = ?
        AND attendance_code = ? AND date_created =  DATE(CURDATE())";

        $execStmt = $this->connection->update(
          $queryStmt, 
          ['sii', $status, $staff_id, $attendance_code]
        );

        if($execStmt) {
          $this->success[] = "Attendance taken successfully!";
          return true;
        }
      } else {
        $this->error['attendance_code_err'] = "Invalid attendance code!";
      }
    }

    return false;
  }

  /**
   * Retrieve Rosters
   *
   * Retrieve all rosters by current date
   * @return array
   **/
  public function retrieveRosters()
  {
    try {
      $queryStmt = "SELECT stf.staff_id, stf.fullname, stf.reg_no,
      ros.staff_id, ros.duty_id, ros.status, ros.attendance_code,  
      ros.date_created, dty.duty, dty.period_from, dty.period_to  
      FROM staffs AS stf INNER JOIN rosters AS ros ON stf.staff_id = ros.staff_id 
      INNER JOIN duties AS dty ON ros.duty_id = dty.duty_id 
      WHERE ros.date_created = DATE(CURDATE())
      ";
      $execStmt = $this->connection->select($queryStmt);

      if ($execStmt) {
        while ($row = $execStmt->fetch_assoc()) {
          $result[] = $row;
        }
      }
    } catch (Exception $e) {
      print "An Error has occurred! Message: {$e->getMessage()}";
    }
    return $result ?? [];
  }
}
