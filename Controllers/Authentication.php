<?php

namespace AutomatedRoster\Controllers;

/**
 * Authenticate user
 */
trait Authentication
{
  /**
   * Authenticate user
   *
   * @return bool
   **/
  public function auth()
  {
    if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
      return false;
    }
    return true;
  }

  /**
   * Check if a user is logged in
   * @return void
   **/
  public function isLogin(Route $route)
  {
    if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
      // redirect
      header("Location: {$route->route('admin/index.php')}");
    }
  }
}
