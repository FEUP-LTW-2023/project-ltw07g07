<?php
require_once('connection.php');
$db = getDataBaseConnection();


// check if the form has been submitted
if (isset($_POST['login'])) {
  // retrieve form data
  $username = $_POST['username'];
  $password = $_POST['password'];

  // find user in the database
  $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
  $stmt->bindParam(':username', $username);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // verify password
  if ($user && password_verify($password, $user['password'])) {
    // login successful, save user ID in session
    session_start();
    $_SESSION['user_id'] = $user['id'];

    // redirect to dashboard
    header('Location: ticket.php');
    exit();
  } else {
    // login failed, show error message
    $error = "Invalid username or password";
  }
}
?>

<!-- login form -->
<form action="" method="POST">
  <label for="username">Username:</label>
  <input type="text" id="username" name="username" required>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required>

  <input type="submit" name="login" value="Login">
</form>
