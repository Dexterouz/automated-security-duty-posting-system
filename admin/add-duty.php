<?php
include 'include.php';

$id = (isset($_GET['id'])) ? htmlspecialchars($_GET['id'], ENT_QUOTES) : null;

// add new duty
if (isset($_POST['add_duty'])) {
  $duty->createDuty($_POST);
}

// update duty
if (isset($_POST['update_duty'])) {
  $duty->updateDuty($_POST);
}

if (isset($_POST['delete_duty'])) {
  $duty->deleteDuty($_POST);
}

$data = (isset($_GET['id'])) ? $duty->fetchDuty($id) : [];

// levels
$levels = [0,1,2,3,4,5];

// fetch all duties
$duties = $duty->fetchDuties();

$error = $duty->error ?? [];
$success = $duty->success ?? [];

echo $twig->render(
  'add-duty.html',
  [
    'title' => 'Add Duty',
    'errors' => $error,
    'success' => $success,
    'duties' => $duties,
    'data' => $data,
    'id' => $id,
    'levels' => $levels
  ]
);
