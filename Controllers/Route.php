<?php

namespace AutomatedRoster\Controllers;

/**
 * Routing
 */
trait Route
{
  /**
   * Route method
   *
   * @param string $path Directory to page    
   * @return string
   **/
  public static function route(string $path = null, string $sub_path = 'automated-security-duty-posting-system/')
  {
    return $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/{$sub_path}" . $path;
  }

  /**
   * Directory method
   *
   * @param string $dirs Choice directory path
   * @param string $root Root path, should be set null when on production server 
   * @return string
   **/
  public function path($dirs = null, $root = "automated-security-duty-posting-system/")
  {
    return $_SERVER['DOCUMENT_ROOT'] . "/{$root}{$dirs}";
  }
}
