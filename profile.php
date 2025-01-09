<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
requireLogin();
$bookingsStorage = new Storage(new JsonIO('data/bookings.json'));
$carsStorage = new Storage(new JsonIO('data/cars.json'));
$user = getUser();
if (isset($_GET['delete_booking'])) {
  $bookingId = $_GET['delete_booking'];
  $booking = $bookingsStorage->findById($bookingId);
  if ($booking && $booking['user_email'] === $user['email']) {
    $bookingsStorage->delete($bookingId);
  }
  header('Location: profile.php');
  exit();
}
$bookings = $bookingsStorage->findAll(['user_email'=>$user['email']]);
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/style.css">
  <title>My Profile</title>
</head>
<body>
<header>
  <div class="container">
    <a href="index.php">iKarRental</a>
    <a href="logout.php" style="margin-left:20px;">Logout</a>
  </div>
</header>
<div class="container">
  <h1>Hello, <?php echo $user['full_name']; ?></h1>
  <h2>My Bookings</h2>
  <?php foreach($bookings as $id => $b): ?>
    <?php
      $car = $carsStorage->findOne(['id' => $b['car_id']]);
    ?>
    <div class="card">
      <p>Car: <?php echo $car ? $car['brand'].' '.$car['model'] : $b['car_id']; ?></p>
      <p>Interval: <?php echo $b['start_date'].' to '.$b['end_date']; ?></p>
      <a href="profile.php?delete_booking=<?php echo $id; ?>" style="color:red;">Delete Booking</a>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
