<?php

session_start();
require_once('connection.php');
$db = getDataBaseConnection();

if (!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    if (strlen($name) < 1) $name = $user['name'];
    $username = $_POST['username'];
    if (strlen($username) < 1) $username = $user['username'];
    $email = $_POST['email'];
    if (strlen($email) < 1) $email = $user['email'];
    
    $stmt = $db->prepare("UPDATE user SET name = :name, username = :username, email = :email WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    
    // Update the $user variable with the new data
    $stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch();
}

?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> Profile </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">
    <script>
        function showForm() {
            document.getElementById("myForm").style.display = "block";
        }
    </script>
  </head>

  <header>
    <h1> <a href="main.php">Trouble Ticket Handler </a></h1>
  </header>


    <p class = "p-prof"> Name: <?=$user['name']?> </p> 
    <p class = "p-prof"> Username: <?=$user['username']?> </p> 
    <p class = "p-prof"> Email: <?=$user['email']?> </p>

    <a href="#" class = "a-prof" onclick="showForm()">Edit profile</a>
    <form id="myForm" style="display: none;" action="" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name"><br>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email"><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password"><br>
        <input type="submit" value="Edit">
        
    </form>

    <br>

    <a href="login.php" class = "a-prof">Logout</a>

    <footer>
        Trouble Ticket
    </footer>
</html>