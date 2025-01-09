<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
$carsStorage=new Storage(new JsonIO('data/cars.json'));
$id=$_GET['id']??'';
$car=null;
$c=$carsStorage->findAll();
foreach($c as $item) {
  if($item['id']==$id) {
    $car=$item; break;
  }
}
if(!$car) {
  header('Location: index.php');
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="css/style.css">
<title><?= $car['brand'].' '.$car['model'] ?></title>
</head>
<body>
<header>
  <div class="container header-links">
    <a href="index.php">iKarRental</a>
    <?php if(isLoggedIn()): ?>
    <a href="profile.php">Profile</a>
    <?php if(isAdmin()): ?>
    <a href="admin.php">Admin</a>
    <?php endif; ?>
    <?php else: ?>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
</header>
<div class="container">
<h1><?= $car['brand'].' '.$car['model'] ?></h1>
<div class="card">
  <img src="<?= $car['image'] ?>">
  <p>Fuel: <?= $car['fuel_type'] ?></p>
  <p>Transmission: <?= $car['transmission'] ?></p>
  <p>Year: <?= $car['year'] ?></p>
  <p>Seats: <?= $car['passengers'] ?></p>
  <p>Price: <?= $car['daily_price_huf'] ?> HUF/day</p>
  <?php if(isLoggedIn()): ?>
  <form id="bookingForm" novalidate>
    <input type="hidden" id="car_id" value="<?= $car['id'] ?>">
    <label>Start date</label>
    <input type="date" id="start_date" required>
    <label>End date</label>
    <input type="date" id="end_date" required>
    <button type="submit">Book</button>
  </form>
  <?php else: ?>
  <p>Please <a href="login.php">login</a> to book.</p>
  <?php endif; ?>
</div>
</div>
<div class="modal-bg" id="modalBg">
  <div class="modal" id="modalContent"></div>
</div>
<script>
let bookedDates=[]
fetch('fetch_booked_dates.php?car_id=<?= $car['id'] ?>')
.then(r=>r.json()).then(d=>{bookedDates=d;});
function isBooked(dt) {
  return bookedDates.includes(dt);
}
function showModal(html) {
  document.getElementById('modalContent').innerHTML=html;
  document.getElementById('modalBg').style.display='flex';
}
document.getElementById('modalBg').addEventListener('click',e=>{
  if(e.target.id==='modalBg') {
    e.target.style.display='none';
  }
});
let start=document.getElementById('start_date');
let end=document.getElementById('end_date');
[start,end].forEach(el=>{
  el.addEventListener('change',()=>{
    if(isBooked(el.value)) {
      alert('This date is already booked.');
      el.value='';
    }
  });
});
let f=document.getElementById('bookingForm');
if(f) {
  f.addEventListener('submit',e=>{
    e.preventDefault();
    let cid=document.getElementById('car_id').value;
    let sd=start.value;
    let ed=end.value;
    if(!sd||!ed) return;
    fetch('booking_handler.php',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({car_id:cid,start_date:sd,end_date:ed})
    })
    .then(r=>r.json())
    .then(resp=>{
      if(resp.success) {
        showModal('<h2>Booking Success</h2><p>'+resp.message+'</p><p>Total cost: '+resp.cost+' HUF</p>');
      } else {
        showModal('<h2>Booking Failed</h2><p>'+resp.message+'</p>');
      }
    })
    .catch(()=>{
      showModal('<h2>Error</h2><p>Could not book.</p>');
    });
  });
}
</script>
</body>
</html>
