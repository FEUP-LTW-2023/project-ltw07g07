<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> Login </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">
  </head>



  <header>
    <h1> Trouble Ticket Handler </h1>
  </header>

  <form action="" method="POST">
  <label for="username">Username:</label>
  <input type="text" id="username" name="username" required>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required>

  <div class="button-container">
      <input type="submit" name="login" value="Login" class = "create-link">
      <a href="new_account.php" class="create-link">Create account</a>
    </div>
  </form>



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
  } else {?>
    <p class = "error-login">Invalid username or password</p>
    <?php
    
    $error = "Invalid username or password";
  }
}
?>

  <footer>
    Trouble Ticket
  </footer>
  </body>
</html>





