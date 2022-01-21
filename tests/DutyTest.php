<?php

use AutomatedRoster\Controllers\Duty;
use PHPUnit\Framework\TestCase;

/**
 * Testing Class
 */
class DutyTest extends TestCase
{
  public function testFetchDuties()
  {
    $duty = new Duty();
    
    $duties = [];
    $result = $duty->fetchDuties();
    foreach ($result as $key) {
      $duties[] = $key['duty'];
    }

    $this->assertCount(3, $duties, print_r($duties));
  }
}
