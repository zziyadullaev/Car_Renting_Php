<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
header('Content-Type: application/json');
if(!isLoggedIn()) {
  echo json_encode(['success'=>false,'message'=>'You must login first']);
  exit();
}
$raw=file_get_contents('php://input');
$data=json_decode($raw,true);
if(!$data) {
  echo json_encode(['success'=>false,'message'=>'Invalid request']);
  exit();
}
$cid=$data['car_id']??'';
$sd=$data['start_date']??'';
$ed=$data['end_date']??'';
if(!$cid||!$sd||!$ed) {
  echo json_encode(['success'=>false,'message'=>'Missing fields']);
  exit();
}
$cars=new Storage(new JsonIO('data/cars.json'));
$car=null;
$allCars=$cars->findAll();
foreach($allCars as $cc) {
  if($cc['id']==$cid) {
    $car=$cc; break;
  }
}
if(!$car) {
  echo json_encode(['success'=>false,'message'=>'Car not found']);
  exit();
}
$bs=strtotime($sd);
$be=strtotime($ed);
if($be<$bs) {
  echo json_encode(['success'=>false,'message'=>'End date is before start date']);
  exit();
}
$bookings=new Storage(new JsonIO('data/bookings.json'));
$existing=$bookings->findAll();
$fail=false;
foreach($existing as $b) {
  if($b['car_id']==$cid) {
    $es=strtotime($b['start_date']);
    $ee=strtotime($b['end_date']);
    if(!($be<$es||$bs>$ee)) {
      $fail=true; break;
    }
  }
}
if($fail) {
  echo json_encode(['success'=>false,'message'=>'Car is already booked in that interval']);
  exit();
}
$user=getUser();
$bookings->add([
  'start_date'=>$sd,
  'end_date'=>$ed,
  'user_email'=>$user['email'],
  'car_id'=>$cid
]);
$days=(($be-$bs)/86400)+1;
$cost=$days*$car['daily_price_huf'];
$msg=$car['brand'].' '.$car['model'].' booked from '.$sd.' to '.$ed;
echo json_encode([
  'success'=>true,
  'message'=>$msg,
  'cost'=>$cost
]);
