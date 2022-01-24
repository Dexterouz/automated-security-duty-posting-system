<?php
include 'include.php';

// Delete a staff
if (isset($_POST['delete'])) {
  $staff->deleteStaff($_POST);
  header("Refresh: 2");
}

// Mark a staff as On/Off leave
if (isset($_POST['mark_leave'])) {
  $staff->markOnLeave($_POST);
  header("Refresh: 2");
}

$staffs = $staff->fetchStaffs();
$success = $staff->success ?? [];
echo $twig->render(
  'all-staff.html',
  [
    'title' => 'All Staff',
    'staffs' => $staffs,
    'success' => $success,
  ]
);
