<?php
require_once 'inc/storage.php';
header('Content-Type: application/json');
$car_id=$_GET['car_id']??'';
$bookings=new Storage(new JsonIO('data/bookings.json'));
$all=$bookings->findAll();
$dates=[];
foreach($all as $b) {
  if($b['car_id']==$car_id) {
    $start=strtotime($b['start_date']);
    $end=strtotime($b['end_date']);
    for($d=$start;$d<=$end;$d+=86400) {
      $dates[]=date('Y-m-d',$d);
    }
  }
}
echo json_encode(array_values(array_unique($dates)));
