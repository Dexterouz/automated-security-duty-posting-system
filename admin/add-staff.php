<?php
include 'include.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $staff->registerStaff($_POST, $_FILES);
  // refresh page in 3s
  header('Refresh: 2');
}

$errors = $staff->error ?? [];
$success = $staff->success ?? [];

echo $twig->render(
  'add-staff.html',
  [
    'title' => 'Add Staff',
    'errors' => $errors,
    'success' => $success,
  ]
);
