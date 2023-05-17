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

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Trouble Ticket Handler</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">

</head>

<body>
<header>
    <h1><a href="admin.php">Trouble Ticket Handler - Admin</a></h1>
</header>

<h1 class="main">MAIN PAGE</h1>

<?php foreach ($users as $user): ?>
      <div id = "user">
        <h3><?= $user['name'] ?></h3>
        <p><?= $user['status'] ?></p>
      </div>
    <?php
  endforeach; ?>

<footer>
   Trouble Ticket
</footer>
</body>
</html>