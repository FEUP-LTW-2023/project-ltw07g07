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

$stmt = $db->prepare("SELECT * FROM faq");
$stmt->execute();
$faq = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title> FAQ </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style2.css">
  </head>

  <header>
    <h1><a href="main.php">Trouble Ticket Handler</a></h1>
  </header>

  <?php foreach ($faq as $f): ?>
  <div id="faq" style = "padding-bottom: 200px">
    <h3 id = title><?= $f['title'] ?></h3>
    <p id = "question"><?= $f['question'] ?></p>
    <p id = "answer" ><?= $f['answer'] ?></p>
  </div>
  <?php endforeach; ?>


  <footer>
    &copy; Trouble Ticket
  </footer>
</html>