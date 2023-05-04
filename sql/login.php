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

<form action="" method="POST">
  <label for="username">Username:</label>
  <input type="text" id="username" name="username" required>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required>

  <input type="submit" name="login" value="Login">
</form>
<a href="new_account.php">Create account</a>
