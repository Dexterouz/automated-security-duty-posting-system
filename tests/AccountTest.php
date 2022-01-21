<?php

use AutomatedRoster\Controllers\Account;
use PHPUnit\Framework\TestCase;

/**
 * Testing Class
 */
class AccountTest extends TestCase
{
  public function testRegisterAdmin()
  {
    $account = new Account();

    $data = [
      'username' => 'admin',
      'password' => 'admin'
    ];

    $result = $account->registerAdmin($data);
    
    $this->assertEquals(false, $result);
  }
}
