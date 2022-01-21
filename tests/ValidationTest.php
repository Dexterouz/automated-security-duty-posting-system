<?php

use AutomatedRoster\Controllers\Validation;
use PHPUnit\Framework\TestCase;

/**
 * Testing Class
 */
class ValidationTest extends TestCase
{
  use Validation;
  public function testValidateEmail()
  {
    $this->validate_email('dexterouskc@gmail.com');

    $this->assertEquals(true, $this->flag);
  }
}
