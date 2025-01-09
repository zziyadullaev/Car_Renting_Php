<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
requireAdmin();
$carsStorage = new Storage(new JsonIO('data/cars.json'));
$errors = [];
$brand = $_POST['brand'] ?? '';
$model = $_POST['model'] ?? '';
$year = $_POST['year'] ?? '';
$transmission = $_POST['transmission'] ?? '';
$fuel_type = $_POST['fuel_type'] ?? '';
$passengers = $_POST['passengers'] ?? '';
$price = $_POST['daily_price_huf'] ?? '';
$image = $_POST['image'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!$brand||!$model||!$year||!$transmission||!$fuel_type||!$passengers||!$price||!$image) {
    $errors[] = 'All fields required';
  } else {
    $newCar = [
      'id'=>rand(1000,9999),
      'brand'=>$brand,
      'model'=>$model,
      'year'=>(int)$year,
      'transmission'=>$transmission,
      'fuel_type'=>$fuel_type,
      'passengers'=>(int)$passengers,
      'daily_price_huf'=>(int)$price,
      'image'=>$image
    ];
    $carsStorageArray = $carsStorage->findAll();
    $carsStorageArray[] = $newCar;
    $carsStorage->deleteMany(function($x){return true;});
    foreach($carsStorageArray as $nc) {
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
  <title>Add Car</title>
</head>
<body>
<header>
  <div class="container">
    <a href="index.php">iKarRental</a>
    <a href="logout.php" style="margin-left:20px;">Logout</a>
  </div>
</header>
<div class="container">
  <h1>Add New Car</h1>
  <?php foreach($errors as $e): ?>
    <p class="error"><?php echo $e; ?></p>
  <?php endforeach; ?>
  <form method="POST" novalidate>
    <label>Brand</label>
    <input type="text" name="brand" value="<?php echo $brand; ?>">
    <label>Model</label>
    <input type="text" name="model" value="<?php echo $model; ?>">
    <label>Year</label>
    <input type="number" name="year" value="<?php echo $year; ?>">
    <label>Transmission</label>
    <select name="transmission">
      <option value="">Select</option>
      <option value="Automatic" <?php if($transmission=='Automatic') echo 'selected'; ?>>Automatic</option>
      <option value="Manual" <?php if($transmission=='Manual') echo 'selected'; ?>>Manual</option>
    </select>
    <label>Fuel Type</label>
    <input type="text" name="fuel_type" value="<?php echo $fuel_type; ?>">
    <label>Number of Passengers</label>
    <input type="number" name="passengers" value="<?php echo $passengers; ?>">
    <label>Daily Price (HUF)</label>
    <input type="number" name="daily_price_huf" value="<?php echo $price; ?>">
    <label>Image URL</label>
    <input type="text" name="image" value="<?php echo $image; ?>">
    <button type="submit">Add Car</button>
  </form>
</div>
</body>
</html>
