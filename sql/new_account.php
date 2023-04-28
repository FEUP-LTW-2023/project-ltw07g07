<?php
// connect to the database
require_once('connection.php');
$db = getDataBaseConnection();

// check if the form has been submitted
if (isset($_POST['register'])) {
  $name = $_POST['name'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $status = 'Client';

  $stmt = $db->prepare("INSERT INTO user (status, name, username, email, password) VALUES (:status, :name, :username, :email, :password)");
  $stmt->bindParam(':status', $status);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':username', $username);
  $stmt->bindParam(':email', $email);
  $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
  $stmt->execute();  
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