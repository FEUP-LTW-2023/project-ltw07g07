<?php
// connect to the database
$db = new PDO("sqlite:database.db");

// check if the form has been submitted
if (isset($_POST['register'])) {
  // retrieve form data
  $name = $_POST['name'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // insert user into the database
  $stmt = $db->prepare("INSERT INTO users (name, username, email, password) VALUES (:name, :username, :email, :password)");
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':username', $username);
  $stmt->bindParam(':email', $email);
  $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
  $stmt->execute();

  // redirect to login page
  header('Location: login.php');
  exit();
}
?>

<!-- registration form -->
<form action="" method="POST">
  <label for="name">Name:</label>
  <input type="text" id="name" name="name" required>

  <label for="username">Username:</label>
  <input type="text" id="username" name="username" required>

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required>

  <input type="submit" name="register" value="Register">
</form>