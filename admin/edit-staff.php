<?php
include 'include.php';
$id = (int) $_GET['id'] ?? null;

// change passport photo
if (isset($_POST['change_photo'])) {
  $staff->updatePassportPhoto($_POST);
  header("Refresh: 3");
}

// update staff data
if (isset($_POST['update_data'])) {
  $staff->updateStaff($_POST);
  header("Refresh: 3");
}

// Retrieve staff data
$data = $staff->fetchStaff($id);

// redirect to all-staff.php if no id is set
if(is_null($data)) header("Location: all-staff.php");

$errors = $staff->error ?? [];
$success = $staff->success ?? [];

echo $twig->render(
  'edit-staff.html',
  [
    'title' => 'Add Staff',
    'data' => $data,
    'errors' => $errors,
    'success' => $success,
  ]
);
