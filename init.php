<?php
session_start();

use AutomatedRoster\Controllers\Account;
use AutomatedRoster\Controllers\Dashboard;
use AutomatedRoster\Controllers\Duty;
use AutomatedRoster\Controllers\Roster;
use AutomatedRoster\Controllers\Staff;
use AutomatedRoster\Config\Connection;

// autoloader function
function autoloader($classname)
{
    $lastSlash = strpos($classname, '\\');
    $classname = substr($classname, $lastSlash);
    $directory = str_replace('\\', '/', $classname);
    $filename = __DIR__ . '/' . $directory . '.php';

    require_once $filename;
}

// autloader
spl_autoload_register('autoloader');
require_once 'vendor/autoload.php';

$account = new Account();
$staff = new Staff();
$duty = new Duty();
$roster = new Roster();
$dashboard = new Dashboard();
$conn = new Connection();

function auth()
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
function isLogin($url)
{
  if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    // redirect
    header("Location: {$url}");
  }
}