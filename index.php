<?php
include 'include.php';
$auth = auth();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $roster->markAttendance($_POST);
  header("REFRESH: 3");
}
$error = $roster->error ?? '';
$success = $roster->success ?? '';

$roster = $roster->retrieveRosters();

echo $twig->render(
  'index.html',
  [
    'title' => 'Home',
    'auth' => $auth,
    'rosters' => $roster,
    'error' => $error,
    'success' => $success,
  ]
);
