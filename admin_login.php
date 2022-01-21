<?php
include 'include.php';
if(auth()) header("Location: index.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $account->login($_POST);
}

$errors = $account->error ?? [];

echo $twig->render(
  'admin_login.html',
  [
    'title' => 'Admin Login',
    'errors' => $errors,
  ]
);
