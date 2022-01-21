<?php

use AutomatedRoster\Controllers\Staff;
use PHPUnit\Framework\TestCase;

/**
 * Testing Class
 */
class StaffTest extends TestCase
{
  public function testFetchStaff()
  {
    $staff = new Staff();
    
    $result = [];
    foreach ($staff->fetchStaffs() as $data) {
      $result[] = $data['staff_id'];
    }

    $this->assertCount(4, $result, print_r($result));
  }
}
