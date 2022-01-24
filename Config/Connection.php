<?php

namespace AutomatedRoster\Config;

use mysqli;
use Exception;

/**
 * Connection to database and execution of mysql queries
 */
class Connection
{
  private $connection = null;
  private $dbhost = "localhost";
  private $dbuser = "root";
  private $dbpassword = "";
  private $dbname = "automated_roster_test_db";

  // Constructor
  public function __construct()
  {
    try {
      $this->setEnvironment("production");
      $this->connection = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
      if ($this->connection->connect_errno) {
        throw new Exception("Unable to connect to database...");
      }
    } catch (Exception $e) {
      print "An Error has occured. Message: {$e->getMessage()}";
    }
  }

  /**
   * Set environment 
   *
   * @param string $environment Name of the environment
   * @return bool
   **/
  public function setEnvironment(string $environment = "development")
  {
    if ($environment == "production") {
      $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
      $this->dbhost = $url["host"];
      $this->dbuser = $url["user"];
      $this->dbpassword = $url["pass"];
      $this->dbname = substr($url["path"], 1);

      return true;
    } else {
      return false;
    }
  }

  /**
   * Query Exectution Method
   *
   * This is method is responisble for executing
   * mysql query    
   *
   * @param string $query Stores SQL query
   * @param array $params Stores query parameter
   * @return object $stmt
   * @throws Exception
   **/
  public function executeStatement($query = "", $params = [])
  {
    try {
      $stmt = $this->connection->prepare($query);

      if ($stmt === false) {
        throw new Exception("Prepare statement failed: " . $query);
      }
      if ($params) {
        $tmp = [];
        foreach ($params as $key => $value) {
          $tmp[$key] = &$params[$key];
        }
        call_user_func_array(array($stmt, 'bind_param'), $tmp);
      }

      $stmt->execute();

      return $stmt;
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  /**
   * SQl insert method
   *
   * Method for executing SQL insert statment
   *
   * @param string $query Stores SQL query
   * @return object
   * @throws Exception
   **/
  public function insert($query = "", $params = [])
  {
    try {
      $stmt = $this->executeStatement($query, $params);
      // return the result of insert statment execution
      // which is in boolean
      return $stmt;
      // close the connection to mysqli
      $stmt->close();
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }

    // return false when there is an error
    return false;
  }

  /**
   * SQL select method
   *
   * Method for executing SQL select statement
   *
   * @param string $query Stores SQL query
   * @return object 
   * @throws Exception
   **/
  public function select($query = "", $params = [])
  {
    try {
      $stmt = $this->executeStatement($query, $params);
      $result = $stmt->get_result();
      if (!$result) {
        throw new Exception("Error in select statement", 1);
      }
      // close mysql connection
      $stmt->close();

      // return result in array
      return $result;
    } catch (Exception $e) {
      // throw new Exception($e->getMessage());
      print "An error occurred " . $e->getMessage();
    }

    // return false when there is an error
    return false;
  }

  /**
   * SQL update method
   *
   * Method for executing SQL update statement
   *
   * @param string $query Stores SQL query
   * @return object
   * @throws Exception
   **/
  public function update($query = "", $params = [])
  {
    try {
      $stmt = $this->executeStatement($query, $params);
      // close connection to mysqli
      $stmt->close();

      // return the result of insert statment execution
      // which is in boolean
      return $stmt;
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }

    // return false when there is an error
    return false;
  }

  /**
   * SQL delete method
   *
   * Method for executing SQL delete statement
   *
   * @param string $query Stores SQL query
   * @return object
   * @throws object
   **/
  public function delete($query = "", $params = [])
  {
    try {
      $stmt = $this->executeStatement($query, $params);
      // close connection to mysql 
      $stmt->close();

      // return the result of insert statment execution
      // which is in boolean
      return $stmt;
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }

    // return false upon error
    return false;
  }
}
