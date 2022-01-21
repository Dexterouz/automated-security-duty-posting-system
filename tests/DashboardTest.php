<?php

use AutomatedRoster\Controllers\Dashboard;
use PHPUnit\Framework\TestCase;

/**
 * Testing Class
 */
class DashboardTest extends TestCase
{
  public function testTotalStaff()
  {
    $dashboard = new Dashboard();

    $result = $dashboard->totalStaff();
    
    $this->assertEquals(4, $result);
  }

  public function testTotalStaffPresent()
  {
    $dashboard = new Dashboard();

    $result = $dashboard->totalStaffPresent();
    
    $this->assertEquals(2, $result);
  }

  public function testTotalStaffAbsent()
  {
    $dashboard = new Dashboard();

    $result = $dashboard->totalStaffAbsent();
    
    $this->assertEquals(2, $result);
  }

  public function testExportCSV()
  {
    $dashboard = new Dashboard();

    $data = [
      'month' => 'december',
      'year' => '2021'
    ];

    $result = $dashboard->exportCSV($data);
    
    $this->assertEquals(true, $result);
  }

  public function testFetchData()
  {
    $dashboard = new Dashboard();

    $data = [
      'month' => 'december',
      'year' => '2021'
    ];

    $result = $dashboard->fetchData('december', '2021');
    
    $this->assertCount(24, $result, print_r($result));
  }
}
