<?php
require_once('connection.php');
$db = getDataBaseConnection();

$errors = array();

$stmt = $db->prepare("SELECT * FROM user where status = 'Admin'");
$stmt->execute();
$admins = $stmt->fetchAll();
$numAdmins = count($admins);

if (isset($_POST['register'])) {
  $name = $_POST['name'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  if ($numAdmins == 0){
    $status = $_POST['status'];
  }
  else {
    $status = 'Client';
  }

  $stmt = $db->prepare("SELECT * FROM user");
  $stmt->execute();
  $users = $stmt->fetchAll();

  $okU = true;
  $okE = true;

  foreach ($users as $user){
    if ($user['username'] == $username){
      $okU = false;
      $errors[] = "Username already in use!";
    }
    if ($user['email'] == $email){
      $okE = false;
      $errors[] = "Email already in use!";
    }
  } 

  if ($okE == true && $okU == true){
    $stmt = $db->prepare("INSERT INTO user (status, name, username, email, password) VALUES (:status, :name, :username, :email, :password)");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
    $stmt->execute();  
  
    session_start();
    $_SESSION['user_id'] = $user['id'];
  
    header('Location: login.php');
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> Register </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">
  </head>

  <header>
    <h1> Trouble Ticket Handler </h1>
  </header>

  <form action="" method="POST">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <?php if (in_array("Username already in use!", $errors)){
      echo ("<p class='error'>Username already in use!</p>");
    }?>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <?php if (in_array("Email already in use!", $errors)){
      echo ("<p class='error'>Email already in use!</p>");
    }?>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <?php if ($numAdmins == 0):?>
      <label for="status">Status:</label>
      <select id="status" name="status">
        <option value="Client">Client</option>
        <option value="Agent">Agent</option>
        <option value="Admin">Admin</option>
      </select>
    <?php endif; ?>

    <input type="submit" name="register" value="Register">
  </form>

  <footer>
    &copy; Trouble Ticket
  </footer>
  
</html>
