<?php
session_start();
require_once('connection.php');
$db = getDataBaseConnection();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


$stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();

if ($user['status'] != 'Admin'){
  header('Location: login.php');
  exit();
}

$stmt = $db->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $stmt = $db->prepare("INSERT INTO department (name) VALUES (:name)");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
  } 
  
  elseif (isset($_POST['remove'])) {
    $name = $_POST['name'];
    $stmt = $db->prepare("DELETE FROM department WHERE name = :name");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
  }
}

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Trouble Ticket Handler</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">
    <script>
        function showForm() {
            document.getElementById("myForm").style.display = "block";
        }
        function showFormRem() {
            document.getElementById("myForm2").style.display = "block";
        }
    </script>

</head>

<body>
<header>
    <h1><a href="admin.php">Trouble Ticket Handler - Admin</a></h1>
</header>

<?php foreach ($users as $user): ?>
      <div id = "user">
        <h3><?= $user['name'] ?></h3>
        <p><?= $user['status'] ?></p>
      </div>
    <?php
  endforeach; ?>

<a href="#" class = "a-prof" onclick="showForm()">Add Department</a>
<form id="myForm" style="display: none;" action="" method="post">
        <label for="name">Department:</label>
        <input type="text" id="name" name="name"><br>
        <input type="submit" name="add" value="Add">
        
</form>

<a href="#" class = "a-prof" onclick="showFormRem()">Remove Department</a>
<form id="myForm2" style="display: none;" action="" method="post">
        <label for="name">Department:</label>
        <input type="text" id="name" name="name"><br>
        <input type="submit" name="remove" value="Remove">
        
</form>

<footer>
   Trouble Ticket
</footer>
</body>
</html>