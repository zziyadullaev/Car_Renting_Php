<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
requireAdmin();
$carsStorage = new Storage(new JsonIO('data/cars.json'));
$bookingsStorage = new Storage(new JsonIO('data/bookings.json'));
$carId = $_GET['id'] ?? '';
$errors = [];
$car = null;
$carsAll = $carsStorage->findAll();
foreach($carsAll as $c) {
  if ($c['id'] == $carId) {
    $car = $c;
    break;
  }
}
if (!$car) {
  header('Location: admin.php');
  exit();
}
if (isset($_GET['delete'])) {
  $filteredCars = [];
  foreach($carsAll as $c) {
    if ($c['id'] != $carId) {
      $filteredCars[] = $c;
    }
  }
  $carsStorage->deleteMany(function($x){return true;});
  foreach($filteredCars as $c) {
    $carsStorage->add($c);
  }
  $bookingsStorage->deleteMany(function($b) use ($carId){
    return $b['car_id'] == $carId;
  });
  header('Location: admin.php');
  exit();
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $brand = $_POST['brand'] ?? '';
  $model = $_POST['model'] ?? '';
  $year = $_POST['year'] ?? '';
  $transmission = $_POST['transmission'] ?? '';
  $fuel_type = $_POST['fuel_type'] ?? '';
  $passengers = $_POST['passengers'] ?? '';
  $price = $_POST['daily_price_huf'] ?? '';
  $image = $_POST['image'] ?? '';
  if (!$brand || !$model || !$year || !$transmission || !$fuel_type || !$passengers || !$price || !$image) {
    $errors[] = 'All fields required';
  } else {
    $updatedCar = [
      'id'=>$car['id'],
      'brand'=>$brand,
      'model'=>$model,
      'year'=>(int)$year,
      'transmission'=>$transmission,
      'fuel_type'=>$fuel_type,
      'passengers'=>(int)$passengers,
      'daily_price_huf'=>(int)$price,
      'image'=>$image
    ];
    $newList = [];
    foreach($carsAll as $c) {
      if ($c['id'] == $carId) {
        $newList[] = $updatedCar;
      } else {
        $newList[] = $c;
      }
    }
    $carsStorage->deleteMany(function($x){return true;});
    foreach($newList as $nc) {
      $carsStorage->add($nc);
    }
    header('Location: admin.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/style.css">
  <title>Edit Car</title>
</head>
<body>
<header>
  <div class="container">
    <a href="index.php">iKarRental</a>
    <a href="logout.php" style="margin-left:20px;">Logout</a>
  </div>
</header>
<div class="container">
  <h1>Edit Car #<?php echo $carId; ?></h1>
  <?php foreach($errors as $e): ?>
    <p class="error"><?php echo $e; ?></p>
  <?php endforeach; ?>
  <form method="POST" novalidate>
    <label>Brand</label>
    <input type="text" name="brand" value="<?php echo $car['brand']; ?>">
    <label>Model</label>
    <input type="text" name="model" value="<?php echo $car['model']; ?>">
    <label>Year</label>
    <input type="number" name="year" value="<?php echo $car['year']; ?>">
    <label>Transmission</label>
    <select name="transmission">
      <option value="Automatic" <?php if($car['transmission']=='Automatic') echo 'selected'; ?>>Automatic</option>
      <option value="Manual" <?php if($car['transmission']=='Manual') echo 'selected'; ?>>Manual</option>
    </select>
    <label>Fuel Type</label>
    <input type="text" name="fuel_type" value="<?php echo $car['fuel_type']; ?>">
    <label>Number of Passengers</label>
    <input type="number" name="passengers" value="<?php echo $car['passengers']; ?>">
    <label>Daily Price (HUF)</label>
    <input type="number" name="daily_price_huf" value="<?php echo $car['daily_price_huf']; ?>">
    <label>Image URL</label>
    <input type="text" name="image" value="<?php echo $car['image']; ?>">
    <button type="submit">Save</button>
  </form>
  <a href="edit_car.php?id=<?php echo $carId; ?>&delete=1" style="color:red;">Delete this car</a>
</div>
</body>
</html>
