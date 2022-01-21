<?php
include 'include.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  switch ($_POST['doc_type']) {
    case 'csv':
      $dashboard->exportCSV($_POST);
      break;
    case 'pdf':
      $dashboard->exportPDF($_POST);
      break;
    default:
      $dashboard->error['export_err'] = "Invalid input!";
      break;
  }
}

$error = $dashboard->error ?? '';

$roster = $roster->retrieveRosters();

echo $twig->render(
  'dashboard.html',
  [
    'title' => 'Dashboard',
    'rosters' => $roster,
    'total_staff' => $dashboard->totalStaff(),
    'staff_present' => $dashboard->totalStaffPresent(),
    'staff_absent' => $dashboard->totalStaffAbsent(),
    'error' => $error
  ]
);
