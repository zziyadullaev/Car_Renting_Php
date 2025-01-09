<?php
require_once 'inc/storage.php';
require_once 'inc/auth.php';
if (isLoggedIn()) {
  redirect('index.php');
}
$errors = [];
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usersStorage = new Storage(new JsonIO('data/users.json'));
  $user = $usersStorage->findOne(['email'=>$email]);
  if (!$user) {
    $errors[] = 'Invalid credentials';
  } else {
    if (!password_verify($password, $user['password'])) {
      $errors[] = 'Invalid credentials';
    } else {
      login($user);
      redirect('index.php');
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/style.css">
  <title>Login</title>
</head>
<body>
<header>
  <div class="container">
    <a href="index.php">iKarRental</a>
  </div>
</header>
<div class="container">
  <h1>Login</h1>
  <?php foreach($errors as $e): ?>
    <p class="error"><?php echo $e; ?></p>
  <?php endforeach; ?>
  <form method="POST" novalidate>
    <label>Email address</label>
    <input type="email" name="email" value="<?php echo $email; ?>">
    <label>Password</label>
    <input type="password" name="password">
    <button type="submit">Login</button>
  </form>
  <p>Not registered? <a href="register.php">Create an account</a></p>
</div>
</body>
</html>
