<?php
// start session
session_start();

// connect to the database
$db = new PDO("sqlite:database.db");

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
  // redirect to login page
  header('Location: login.php');
  exit();
}

// retrieve user data from the database
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// check if the form has been submitted
if (isset($_POST['update'])) {
  // retrieve form data
  $name = $_POST['name'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // update user data in the database
  $stmt = $db->prepare("UPDATE users SET name = :name, username = :username, email = :email WHERE id = :id");
  $stmt->bindParam(':id', $_SESSION['user_id']);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':username', $username);
  $stmt->bindParam(':email', $email);

  // update password if provided
  if (!empty($password)) {
    $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
  }

  $stmt->execute();

  // redirect to dashboard
  header('Location: dashboard.php');
  exit();
}
?>

<!-- edit profile form -->
<form action="" method="POST">
  <label for="name">Name:</label>
  <input type="text" id="name" name="name" value="<?= $user['name'] ?>" required>

  <label for="username">Username:</label>
  <input type="text" id="username" name="username" value="<?= $user['username'] ?>" required>

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password">

  <input type="submit" name="update" value="Update">
</form>