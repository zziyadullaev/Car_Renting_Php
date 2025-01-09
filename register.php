<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
if (isLoggedIn()) {
  redirect('index.php');
}
$errors = [];
$fullName = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$passwordAgain = $_POST['password_again'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!$fullName || !$email || !$password || !$passwordAgain) {
    $errors[] = 'All fields are required';
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email';
  } else if ($password !== $passwordAgain) {
    $errors[] = 'Passwords do not match';
  } else {
    $usersStorage = new Storage(new JsonIO('data/users.json'));
    $check = $usersStorage->findOne(['email'=>$email]);
    if ($check) {
      $errors[] = 'Email already in use';
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $record = [
        'full_name'=>$fullName,
        'email'=>$email,
        'password'=>$hashed,
        'is_admin'=>false
      ];
      $usersStorage->add($record);
      redirect('login.php');
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/style.css">
  <title>Registration</title>
</head>
<body>
<header>
  <div class="container">
    <a href="index.php">iKarRental</a>
  </div>
</header>
<div class="container">
  <h1>Registration</h1>
  <?php foreach($errors as $e): ?>
    <p class="error"><?php echo $e; ?></p>
  <?php endforeach; ?>
  <form method="POST" novalidate>
    <label>Full Name</label>
    <input type="text" name="full_name" value="<?php echo $fullName; ?>">
    <label>Email Address</label>
    <input type="email" name="email" value="<?php echo $email; ?>">
    <label>Password</label>
    <input type="password" name="password">
    <label>Password again</label>
    <input type="password" name="password_again">
    <button type="submit">Registration</button>
  </form>
</div>
</body>
</html>
