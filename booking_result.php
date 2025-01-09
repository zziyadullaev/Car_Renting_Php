<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
$carsStorage = new Storage(new JsonIO('data/cars.json'));
$fail = $_GET['fail'] ?? '';
$carId = $_GET['car_id'] ?? '';
$startDate = $_GET['start'] ?? '';
$endDate = $_GET['end'] ?? '';
$car = null;
foreach ($carsStorage->findAll() as $c) {
  if ($c['id'] == $carId) {
    $car = $c;
    break;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/style.css">
  <title>Booking result</title>
</head>
<body>
<header>
  <div class="container">
    <a href="index.php">iKarRental</a>
    <?php if(isLoggedIn()): ?>
    <a href="profile.php" style="margin-left:20px;">Profile</a>
    <?php if(isAdmin()): ?>
    <a href="admin.php" style="margin-left:20px;">Admin</a>
    <?php endif; ?>
    <?php else: ?>
    <a href="login.php" style="margin-left:20px;">Login</a>
    <a href="register.php" style="margin-left:20px;">Registration</a>
    <?php endif; ?>
  </div>
</header>
<div class="container">
<?php if($fail): ?>
  <h2>Booking failed!</h2>
  <p>The car is not available in the selected interval or dates were invalid.</p>
  <a href="car_details.php?id=<?php echo $carId; ?>">Back to the vehicle side</a>
<?php else: ?>
  <h2>Successful booking!</h2>
  <?php
  $days = (strtotime($endDate) - strtotime($startDate))/86400 + 1;
  $cost = $days * $car['daily_price_huf'];
  ?>
  <p><?php echo $car['brand'].' '.$car['model'].' has been booked for '.$startDate.' to '.$endDate; ?></p>
  <p>Total price: <?php echo $cost; ?> HUF</p>
  <a href="profile.php">My profile</a>
<?php endif; ?>
</div>
</body>
</html>
