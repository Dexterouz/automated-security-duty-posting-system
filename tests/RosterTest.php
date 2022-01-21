<?php

use AutomatedRoster\Controllers\Roster;
use PHPUnit\Framework\TestCase;

/**
 * Testing Class
 */
class RosterTest extends TestCase
{
  public function testCurrentDay()
  {
    $roster = new Roster();

    $result = $roster->currentDay();

    $this->assertEquals(true, $result);
  }

  public function testMapArray()
  {
    $roster = new Roster();

    $staffs = $roster->fetchStaffs();
    $duties = [];

    foreach ($roster->fetchDuties() as $data) {
      $duties[] = $data['duty'];
    }

    $result = $roster->mapArray($staffs, $duties);

    $this->assertCount(4, $result, print_r($result));
  }

  public function testAssignRoster()
  {
    $roster = new Roster();

    $result = $roster->assignRoster();

    $this->assertEquals(true, $result);
  }

  public function testRetrieveRosters()
  {
    $roster = new Roster();

    $result = $roster->retrieveRosters();

    $this->assertCount(4, $result);
  }

  public function testMarkAttendance()
  {
    $roster = new Roster();

    $data = [
      'staff_id' => 2,
      'attendance_code' => 36709
    ];

    $result = $roster->markAttendance($data);

    $this->assertEquals(true, $result, print_r($roster->error));
  }
}
