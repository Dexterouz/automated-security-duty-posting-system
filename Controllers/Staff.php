<?php

namespace AutomatedRoster\Controllers;

use AutomatedRoster\Config\Connection;
use Exception;

/**
 * Staffs 
 */
class Staff extends Duty
{
  use Validation;

  protected $connection = null;
  private $table = "staffs";

  public function __construct()
  {
    $this->connection = new Connection();
  }

  /**
   * Register new staff
   *
   * @param array $request Form data request
   * @return bool
   * @throws Exception
   **/
  public function registerStaff(array $request): bool
  {
    // dump array as variable
    extract($request);

    // sanitize and validate input
    $fullname = $this->validate_text($fullname, 'fullname');
    $phone = $this->validate_text($phone, 'phone');
    $email = $this->validate_email($email);
    $gender = $this->validate_text($gender, 'gender');
    $experience = $this->validate_text($experience, 'years of experience');
    $age = $this->validate_number($age, 'age');
    $dissabilities = $this->validate_text($dissabilities, 'dissabilities');
    $on_leave = "off";
    $reg_no = "UNN" . rand(10000, 90000);
    $passport_photo = $this->validate_text($photo, 'passport photo');

    // if there's no error
    if ($this->flag) {
      try {
        $ext = '.png';
        $image_name = $reg_no . '-' .time(). $ext;
        $exec_stmt = $this->connection->insert(
          "INSERT INTO {$this->table} 
          (
            fullname, gender, age, phone, 
            email, dissabilities, 
            year_of_experience, on_leave, 
            reg_no, passport
          ) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
          [
            'ssisssssss', $fullname, $gender, 
            $age, $phone, $email, $dissabilities, 
            $experience, $on_leave, $reg_no, $image_name
          ]
        );

        // if true
        if ($exec_stmt) {
          $this->success[] = "New Staff Added Successfully!";

          $directory = "../images/passport/{$image_name}";
          if(!file_put_contents($directory, base64_decode($passport_photo))) {
            throw new Exception("Error in uploading profile image", 1);
          }

          return true;
        } else {
          throw new Exception("Error in adding new staff", 1);
        }
      } catch (Exception $e) {
        print "An Error has occurred! Message: {$e->getMessage()}";
      }
    }
    return false;
  }

  /**
   * Fetch Staffs
   * 
   * Fetch all staffs from the database
   * @return array
   */
  public function fetchStaffs(): array
  {
    $result = [];
    $queryStmt = "SELECT * FROM {$this->table}";
    $execStmt = $this->connection->select($queryStmt);
    while ($row = $execStmt->fetch_assoc()) {
      $result[] = $row;
    }

    return $result;
  }

  /**
   * Fetch Staff
   * 
   * Fetch a staff from the database
   * @return array
   */
  public function fetchStaff(int $id): array
  {
    $id = $this->validate_number($id);
    if ($this->flag) {
      try {
        $result = [];
        $queryStmt = "SELECT * FROM {$this->table} WHERE staff_id = ?";
        $execStmt = $this->connection->select($queryStmt, ['i', $id]);
        while ($row = $execStmt->fetch_assoc()) {
          $result[] = $row;
        }
      } catch (Exception $e) {
        print "An Error occurred! Message: {$e->getMessage()}";
      }
    }
    return $result[0] ?? [];
  }

  /**
   * Retrieve staffs by experience
   *
   * @param int $year Staff years of experience
   * @param string $operator Conditional operator e.g '<', '>', '='
   * @return array
   * @throws Exception
   **/
  public function retrieveStaffByExperience(int $year, string $operator)
  {
    $on_leave = 'off';
    try {
      $result = [];
      $queryStmt = "SELECT * FROM {$this->table} WHERE year_of_experience {$operator} {$year} AND on_leave = ?";
      $execStmt = $this->connection->select($queryStmt, ['s', $on_leave]);

      while ($row = $execStmt->fetch_assoc()) {
        $result[] = $row;
      }
    } catch (Exception $e) {
      print "An Error occurred! Message: {$e->getMessage()}";
    }

    return $result;
  }

  /**
   * Delete a Staff
   *
   * @param array $request Form data request
   * @return bool
   **/
  public function deleteStaff(array $request)
  {
    extract($request);
    $staff_data = $this->fetchStaff($staff_id);

    // delete old passport photo
    $old_passport_photo = "../images/passport/{$staff_data['passport']}";
    unlink($old_passport_photo);

    // delete sql stmt
    $queryStmt = "DELETE FROM {$this->table} WHERE staff_id = ?";
    $execStmt = $this->connection->delete($queryStmt, ['i', $staff_id]);
    if ($execStmt) {
      $this->success[] = "Staff deleted successfully!";
      return true;
    }

    return false;
  }

  /**
   * Update Staff
   *
   * @param array $request Form data request
   * @return bool
   **/
  public function updateStaff(array $request)
  {
    extract($request);
    $staff_id = $this->validate_number($staff_id);
    $fullname = $this->validate_text($fullname, "fullname");
    $phone = $this->validate_text($phone, "phone");
    $email = $this->validate_email($email);
    $gender = $this->validate_text($gender, 'gender');
    $experience = $this->validate_text($experience, 'years of experience');
    $age = $this->validate_number($age, 'age');
    $dissabilities = $this->validate_text($dissabilities, 'dissabilities');

    if ($this->flag) {
      try {
        $queryStmt = "UPDATE {$this->table}
        SET fullname = ?, phone = ?, email = ?,
        gender = ?, age = ?, dissabilities = ?,
        year_of_experience = ? WHERE staff_id = ?";

        $execStmt = $this->connection->update(
          $queryStmt,
          [
            'ssssissi', $fullname, $phone, 
            $email, $gender, $age,  
            $dissabilities, $experience, $staff_id
          ]
        );

        if ($execStmt) {
          $this->success[] = "Staff data updated successfully!";
          return true;
        }
      } catch (Exception $e) {
        print "An Error has occurred! Message: " . $e->getMessage();
      }
    }
  }

  /**
   * Mark a staff On/Off leave
   *
   * @param array $request Form data request
   * @return bool
   **/
  public function markOnLeave(array $request)
  {
    extract($request);
    $id = $this->validate_number($id);
    $on_leave = $this->validate_text($on_leave ?? 'off');
    
    if ($this->flag) {
      $queryStmt = "UPDATE {$this->table} SET on_leave = ? WHERE staff_id = ?";
      $execStmt = $this->connection->update(
        $queryStmt, ['si', $on_leave, $id]
      );

      // fetch staff name
      $staff = $this->fetchStaff($id)['fullname'];

      if ($execStmt) {
        $this->success[] = "Staff {$staff} has been mark {$on_leave} leave";
      }
    }
  }

  /**
   * Update passport photo
   *
   * @param array $request Form data request
   * @param array $file Form data file request
   * @return bool
   **/
  public function updatePassportPhoto(array $request)
  {
    extract($request);

    // sanitize input
    $staff_id = $this->validate_number($staff_id);
    $passport_photo = $this->validate_text($photo, "passport photo");

    // if true
    if ($this->flag) {
      $staff_data = $this->fetchStaff($staff_id); // retrieve staff reg no
      $ext = '.png';
      $new_image_name = $staff_data['reg_no'] . '-' .time(). $ext;

      // delete old passport photo
      $old_passport_photo = "../images/passport/{$staff_data['passport']}";
      unlink($old_passport_photo);

      $queryStmt = "UPDATE {$this->table} SET passport = ? WHERE staff_id = ?";
      $execStmt = $this->connection->update($queryStmt, ['si', $new_image_name, $staff_id]);

      // if true
      if ($execStmt) {
        $this->success[] = "Passport photo updated successfully!";
        $directory = "../images/passport/{$new_image_name}";
        // throw an exception if there's an error
        if(!file_put_contents($directory, base64_decode($passport_photo))) {
          throw new Exception("Error in updating profile image", 1);
        }
        return true;
      }
    }
    return false;
  }
}
