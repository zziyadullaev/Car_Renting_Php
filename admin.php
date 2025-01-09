<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
requireAdmin();
$bookingsStorage = new Storage(new JsonIO('data/bookings.json'));
$carsStorage = new Storage(new JsonIO('data/cars.json'));
$allBookings = $bookingsStorage->findAll();
if (isset($_GET['delete_booking'])) {
  $bid = $_GET['delete_booking'];
  $bookingsStorage->delete($bid);
  header('Location: admin.php');
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/style.css">
  <title>Admin Panel</title>
</head>
<body>
<header>
  <div class="container">
    <a href="index.php">iKarRental</a>
    <a href="logout.php" style="margin-left:20px;">Logout</a>
  </div>
</header>
<div class="container">
  <h1>Admin Panel</h1>
  <a href="add_car.php">Add New Car</a>
  <h2>All Bookings</h2>
  <?php foreach($allBookings as $id=>$b): ?>
    <?php
      $car = null;
      foreach ($carsStorage->findAll() as $c) {
        if ($c['id'] == $b['car_id']) {
          $car = $c;
          break;
        }
      }
    ?>
    <div class="card">
      <p>ID: <?php echo $id; ?></p>
      <p>User: <?php echo $b['user_email']; ?></p>
      <p>Car: <?php echo $car ? $car['brand'].' '.$car['model'] : $b['car_id']; ?></p>
      <p>Interval: <?php echo $b['start_date'].' - '.$b['end_date']; ?></p>
      <a href="admin.php?delete_booking=<?php echo $id; ?>">Delete Booking</a>
    </div>
  <?php endforeach; ?>
  <h2>All Cars</h2>
  <?php $allCars = $carsStorage->findAll(); ?>
  <?php foreach($allCars as $c): ?>
    <div class="card">
      <p>ID: <?php echo $c['id']; ?></p>
      <p><?php echo $c['brand'].' '.$c['model'].' ('.$c['year'].')'; ?></p>
      <p><?php echo $c['transmission'].' - '.$c['fuel_type'].' - '.$c['passengers'].' seats'; ?></p>
      <p><?php echo $c['daily_price_huf'].' HUF/day'; ?></p>
      <a href="edit_car.php?id=<?php echo $c['id']; ?>">Edit</a>
      <a href="edit_car.php?id=<?php echo $c['id']; ?>&delete=1" style="color:red;">Delete</a>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
