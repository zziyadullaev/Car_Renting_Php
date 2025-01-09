<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
$carsStorage = new Storage(new JsonIO('data/cars.json'));
$bookingsStorage = new Storage(new JsonIO('data/bookings.json'));
$transmission = $_GET['trans'] ?? '';
$passengers = $_GET['passengers'] ?? '';
$priceMin = $_GET['price_min'] ?? '';
$priceMax = $_GET['price_max'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
function isCarAvailable($carId, $from, $to, $bookings) {
  if (!$from || !$to) return true;
  foreach ($bookings->findAll() as $b) {
    if ($b['car_id'] == $carId) {
      $bookStart = strtotime($b['start_date']);
      $bookEnd = strtotime($b['end_date']);
      $checkStart = strtotime($from);
      $checkEnd = strtotime($to);
      if (!($checkEnd < $bookStart || $checkStart > $bookEnd)) {
        return false;
      }
    }
  }
  return true;
}
$cars = $carsStorage->findAll();
$filtered = [];
foreach ($cars as $c) {
  if ($transmission && strtolower($c['transmission']) != strtolower($transmission)) continue;
  if ($passengers && $c['passengers'] < $passengers) continue;
  if ($priceMin && $c['daily_price_huf'] < $priceMin) continue;
  if ($priceMax && $c['daily_price_huf'] > $priceMax) continue;
  if (!isCarAvailable($c['id'], $dateFrom, $dateTo, $bookingsStorage)) continue;
  $filtered[] = $c;
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/style.css">
  <title>iKarRental</title>
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
  <h1>Rent cars easily!</h1>
  <form method="GET" novalidate>
    <label>Transmission</label>
    <select name="trans">
      <option value="">Any</option>
      <option value="Automatic" <?php if($transmission=="Automatic") echo "selected"; ?>>Automatic</option>
      <option value="Manual" <?php if($transmission=="Manual") echo "selected"; ?>>Manual</option>
    </select>
    <label>Minimum seats</label>
    <input type="number" name="passengers" value="<?php echo $passengers; ?>">
    <label>Date from</label>
    <input type="date" name="date_from" value="<?php echo $dateFrom; ?>">
    <label>Date to</label>
    <input type="date" name="date_to" value="<?php echo $dateTo; ?>">
    <label>Price min (HUF)</label>
    <input type="number" name="price_min" value="<?php echo $priceMin; ?>">
    <label>Price max (HUF)</label>
    <input type="number" name="price_max" value="<?php echo $priceMax; ?>">
    <button type="submit">Filter</button>
  </form>
  <div class="flex">
    <?php foreach ($filtered as $car): ?>
    <div class="card">
      <img src="<?php echo $car['image']; ?>" style="max-width:100%;">
      <h2><?php echo $car['brand'].' '.$car['model']; ?></h2>
      <p><?php echo $car['year'].' - '.$car['transmission'].' - '.$car['passengers'].' seats'; ?></p>
      <p><?php echo $car['daily_price_huf'].' HUF/day'; ?></p>
      <a href="car_details.php?id=<?php echo $car['id']; ?>">View details</a>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
