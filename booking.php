<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
requireLogin();
$carId = $_POST['car_id'] ?? '';
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';
if (!$carId || !$startDate || !$endDate) {
  header('Location: car_details.php?id='.$carId);
  exit();
}
$carsStorage = new Storage(new JsonIO('data/cars.json'));
$bookingsStorage = new Storage(new JsonIO('data/bookings.json'));
$car = null;
foreach ($carsStorage->findAll() as $c) {
  if ($c['id'] == $carId) {
    $car = $c;
    break;
  }
}
if (!$car) {
  header('Location: index.php');
  exit();
}
$existing = $bookingsStorage->findAll();
$failed = false;
$newStart = strtotime($startDate);
$newEnd = strtotime($endDate);
if ($newEnd < $newStart) {
  $failed = true;
} else {
  foreach ($existing as $b) {
    if ($b['car_id'] == $carId) {
      $bookStart = strtotime($b['start_date']);
      $bookEnd = strtotime($b['end_date']);
      if (!($newEnd < $bookStart || $newStart > $bookEnd)) {
        $failed = true;
        break;
      }
    }
  }
}
if (!$failed) {
  $bookingsStorage->add([
    'start_date'=>$startDate,
    'end_date'=>$endDate,
    'user_email'=>getUser()['email'],
    'car_id'=>$carId
  ]);
}
header('Location: booking_result.php?car_id='.$carId.'&start='.$startDate.'&end='.$endDate.'&fail='.(int)$failed);
exit();
