<?php

namespace AutomatedRoster\Controllers;

use AutomatedRoster\Controllers\Validation;
use AutomatedRoster\Config\Connection;
use AutomatedRoster\Controllers\Route;
use Exception;


/**
 * Handles Registration, Login and Logout
 */
class Account
{
  use Validation;
  use Route;
  
  protected $connection = null;

  public function __construct()
  {
    $this->connection = new Connection();
  }

  /**
   * Fetch admin data
   *
   * @param string $table_column Name of the column to fetch
   * @param string $where SQL where clause
   * @param mixed $param Where clause parameter
   * @return array
   * @throws Exception
   **/
  public function fetch(string $table_column, string $where, mixed $param)
  {
    try {
      $exec_stmt = $this->connection->select(
        "SELECT {$table_column} FROM admins WHERE {$where} = ?",
        ['s', $param]
      );
      if ($exec_stmt) {
        // loop array
        while ($row = $exec_stmt->fetch_assoc()) {
          $result[] = $row;
        }
      }
    } catch (Exception $e) {
      print "An Error has occurred! Message: {$e->getMessage()}";
    }

    return $result[0] ?? [];
  }

  /**
   * check username
   *
   * Checking if username already existed
   *
   * @param string $username username
   * @return bool
   **/
  public function fetchUsername(string $username)
  {
    // execute the query
    $exec_stmt = $this->connection->select("SELECT * FROM admins WHERE username = ?", ['s', $username]);

    // if username is found return true
    if ($exec_stmt->num_rows == 1) {
      return true;
    }

    // return false if not found
    return false;
  }

  /**
   * Register
   *
   * Register new admin
   *
   * @param array $request Form data request
   * @return bool
   * @throws Exception
   **/
  public function registerAdmin(array $request = null): bool
  {
    // dump array as variable
    extract($request);

    // sanitize and validate input
    $check = $this->fetchUsername($username);
    $username = $this->validate_username($username, 'username', (!empty($check)));
    $password = $this->validate_text($password, 'password');

    // if there's no error
    if ($this->flag) {
      try {
        // hash the password
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        // 
        $exec_stmt = $this->connection->insert(
          "INSERT INTO admins (username, password, created_at) VALUES (?, ?, NOW())",
          ['ss', $username, $hash_password]
        );

        // if true
        if ($exec_stmt) {
          $this->success[] = "New admin registered successfully!";
          return true;
        } else {
          throw new Exception("Error in registering new admin", 1);
        }
      } catch (Exception $e) {
        print "An Error has occurred! Message: {$e->getMessage()}";
      }
    }
    return false;
  }

  /**
   * Login
   *
   * Login admin
   *
   * @param array $request Form data request
   * @return void
   * @throws Exception
   **/
  public function login(array $request = null)
  {
    // dump array as variable
    extract($request);

    // sanitize and validate input
    $username = $this->validate_text($username, 'username');
    $password = $this->validate_text($password, 'password');

    // if there's no error
    if ($this->flag) {
      // retrieve user data from the database
      $fetch = $this->fetch('admin_id, username, password', 'username', $username);

      // if its not empty
      if (!empty($fetch)) {

        // verify password
        if (password_verify($password, $fetch['password'])) {
          // start session
          session_start();

          // create session variable to store user ID, email
          $_SESSION['id'] = $fetch['admin_id'];
          $_SESSION['username'] = $fetch['username'];

          // redirect url
          $url = $this->route("admin/dashboard.php");

          // redirect
          header("Location: {$url}");
        } else {
          $this->error['login_err'] = "Incorrect username or password";
        }
      } else {
        $this->error['login_err'] = "Incorrect username or password";
      }
    }
  }

  /**
   * Log out user
   *
   **/
  public function logout()
  {
    if (session_status() == PHP_SESSION_ACTIVE) {
      unset($_SESSION['id']); // Unset ID
      unset($_SESSION['username']); // Unset username

      // redirect to login
      header("Location: " . $this->route('index.php'));
    }
  }
}
