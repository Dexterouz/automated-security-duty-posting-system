<?php
include 'init.php';

$data = [
  'username' => 'admin',
  'password' => 'admin'
];

$account->registerAdmin($data);