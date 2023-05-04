<?php
require_once('connection.php');
$db = getDataBaseConnection();


if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
  $stmt->bindParam(':username', $username);
  $stmt->execute();
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    session_start();
    $_SESSION['user_id'] = $user['id'];

    header('Location: main.php');
    exit();
  } else {
    echo ("Invalid username or password");
    $error = "Invalid username or password";
  }
}
?>


<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> Login </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
  </head>



  <header>
    <h1> Trouble Ticket handler </h1>
    <h1> $ </h1>




  </header>
  
  

  <form action="" method="POST">
  <label for="username">Username:</label>
  <input type="text" id="username" name="username" required>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required>

  <input type="submit" name="login" value="Login">
  </form>
  <a href="new_account.php">Create account</a>




  <footer>
    LTW ticket project 2023
  </footer>
  </body>
</html>





